<?php

namespace App\Console\Commands;

use App\Models\FlowerType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DownloadFlowerImages extends Command
{
    protected $signature = 'flowers:download-images {--force : Replace existing images}';
    protected $description = 'Wikimedia Commons üzerinden çiçek görsellerini indirir ve FlowerType kayıtlarına bağlar.';

    /** Slug → Wikimedia Commons arama terimi(leri). Sıralı dener. */
    protected array $queries = [
        'antoryum-kirmizi'                  => ['Anthurium andraeanum red', 'Anthurium red', 'Red anthurium'],
        'yesil-antoryum'                    => ['Anthurium green', 'Green anthurium', 'Anthurium andraeanum green'],
        'ammi-visnaga'                      => ['Ammi visnaga flower', 'Visnaga daucoides', 'Ammi visnaga'],
        'karanfil-kelle-pembe'              => ['Pink carnation flower', 'Dianthus caryophyllus pink', 'Pink Dianthus caryophyllus'],
        'karanfil-kelle-sari'               => ['Yellow carnation flower', 'Dianthus caryophyllus yellow', 'Yellow Dianthus caryophyllus'],
        'karanfil-kelle-yesil'              => ['Green carnation flower', 'Dianthus caryophyllus green'],
        'sari-lisianthus'                   => ['Eustoma yellow', 'Yellow lisianthus', 'Eustoma grandiflorum yellow'],
        'mor-lisianthus'                    => ['Eustoma purple', 'Purple lisianthus', 'Eustoma grandiflorum purple'],
        'pembe-lisianthus'                  => ['Eustoma pink', 'Pink lisianthus', 'Eustoma grandiflorum pink'],
        'koyu-mavi-delphinium'              => ['Dark blue delphinium', 'Delphinium dark blue', 'Delphinium elatum blue'],
        'pembe-delphinium'                  => ['Pink delphinium', 'Delphinium pink', 'Delphinium elatum pink'],
        'beyaz-delphinium'                  => ['White delphinium', 'Delphinium white', 'Delphinium elatum white'],
        'katmerli-bebe-mavisi-delphinium'   => ['Light blue delphinium', 'Delphinium light blue double'],
        'sari-anastasia'                    => ['Anastasia yellow chrysanthemum', 'Yellow spider chrysanthemum', 'Chrysanthemum yellow spider'],
        'yesil-anastasia'                   => ['Green anastasia chrysanthemum', 'Green spider chrysanthemum', 'Chrysanthemum green'],
        'statis-pembe'                      => ['Limonium pink', 'Pink statice', 'Limonium sinuatum pink'],
        'statis-koyu-pembe'                 => ['Limonium dark pink', 'Limonium sinuatum dark pink', 'Statice pink'],
        'statis-sari'                       => ['Limonium yellow', 'Yellow statice', 'Limonium sinuatum yellow'],
        'statis-mor'                        => ['Limonium purple', 'Purple statice', 'Limonium sinuatum'],
        'statis-beyaz'                      => ['Limonium white', 'White statice', 'Limonium sinuatum white'],
        'beyaz-karanfil-sprey'              => ['White spray carnation', 'Dianthus caryophyllus white', 'White Dianthus'],
        'sari-karanfil-sprey'               => ['Yellow spray carnation', 'Yellow mini carnation'],
        'lepidium'                          => ['Lepidium plant', 'Lepidium sativum', 'Lepidium flower'],
        'pembe-fisfirik'                    => ['Astilbe pink', 'Pink astilbe flower', 'Astilbe arendsii pink'],
        'soli'                              => ['Solidago canadensis', 'Goldenrod solidago', 'Solidago plant'],
        'pembe-gerbera'                     => ['Pink gerbera', 'Gerbera jamesonii pink', 'Gerbera pink flower'],
        'somon-pembe-gerbera'               => ['Salmon gerbera', 'Gerbera salmon', 'Gerbera coral'],
        'turuncu-gerbera'                   => ['Orange gerbera', 'Gerbera jamesonii orange', 'Gerbera orange flower'],
        'pembe-sebboy'                      => ['Pink stock flower Matthiola', 'Matthiola incana pink', 'Pink Matthiola'],
        'lila-sebboy'                       => ['Lilac stock flower Matthiola', 'Matthiola incana lilac', 'Purple Matthiola'],
        'somon-sebboy'                      => ['Salmon stock flower Matthiola', 'Matthiola incana salmon'],
        'yosun'                             => ['Sphagnum moss', 'Floral moss', 'Hypnum moss'],
    ];

    public function handle(): int
    {
        $disk = Storage::disk('public');
        $disk->makeDirectory('flowers');

        $flowers = FlowerType::orderBy('id')->get();
        $bar = $this->output->createProgressBar($flowers->count());
        $bar->start();

        $ok = 0; $skipped = 0; $failed = [];

        foreach ($flowers as $flower) {
            $bar->advance();

            if ($flower->image_path && !$this->option('force')) {
                $skipped++;
                continue;
            }

            $queries = $this->queries[$flower->slug] ?? [$flower->name];
            $imageUrl = null;
            foreach ($queries as $q) {
                $imageUrl = $this->searchWikimedia($q);
                if ($imageUrl) break;
            }

            if (!$imageUrl) {
                $failed[] = $flower->name;
                continue;
            }

            $resp = Http::withHeaders([
                'User-Agent' => 'YineMiCicek/1.0 (https://yinemicicek.com; admin@yinemicicek.com)',
            ])->timeout(30)->get($imageUrl);

            if (!$resp->successful()) {
                $failed[] = $flower->name . ' (download)';
                continue;
            }

            $ext = $this->extensionFromUrl($imageUrl) ?: 'jpg';
            $path = 'flowers/' . $flower->slug . '.' . $ext;
            $disk->put($path, $resp->body());

            if ($flower->image_path && $flower->image_path !== $path) {
                $disk->delete($flower->image_path);
            }

            $flower->image_path = $path;
            $flower->save();
            $ok++;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ İndirildi: $ok");
        $this->info("⏭️  Atlandı (zaten görseli vardı): $skipped");
        if ($failed) {
            $this->warn('❌ Bulunamadı: ' . implode(', ', $failed));
        }

        return self::SUCCESS;
    }

    /**
     * Wikimedia Commons API'sinde resim ara, en uygun resmin thumbnail URL'ini döner.
     */
    protected function searchWikimedia(string $query): ?string
    {
        $resp = Http::withHeaders([
            'User-Agent' => 'YineMiCicek/1.0 (https://yinemicicek.com; admin@yinemicicek.com)',
        ])->timeout(20)->get('https://commons.wikimedia.org/w/api.php', [
            'action'      => 'query',
            'format'      => 'json',
            'generator'   => 'search',
            'gsrsearch'   => 'filetype:bitmap ' . $query,
            'gsrnamespace'=> 6,
            'gsrlimit'    => 5,
            'prop'        => 'imageinfo',
            'iiprop'      => 'url|mime',
            'iiurlwidth'  => 600,
        ]);

        if (!$resp->successful()) return null;

        $pages = data_get($resp->json(), 'query.pages', []);
        if (!$pages) return null;

        // Sort by index from the search generator so first result wins
        usort($pages, fn ($a, $b) => ($a['index'] ?? 999) <=> ($b['index'] ?? 999));

        foreach ($pages as $page) {
            $info = $page['imageinfo'][0] ?? null;
            if (!$info) continue;
            $mime = $info['mime'] ?? '';
            if (!str_starts_with($mime, 'image/')) continue;
            if (str_contains($mime, 'svg')) continue;
            return $info['thumburl'] ?? $info['url'] ?? null;
        }

        return null;
    }

    protected function extensionFromUrl(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) ? $ext : null;
    }
}
