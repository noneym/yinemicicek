<?php

namespace App\Console\Commands;

use App\Models\BouquetType;
use App\Models\FlowerType;
use App\Models\TableType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MigrateMediaToR2 extends Command
{
    protected $signature = 'media:migrate-to-r2 {--from=public : Kaynak disk} {--delete : Yüklendikten sonra kaynak dosyayı sil}';
    protected $description = 'Yerel diskteki çiçek/masa/buket görsellerini R2 bucketına kopyalar.';

    public function handle(): int
    {
        $source = Storage::disk($this->option('from'));
        $r2 = Storage::disk('r2');

        $models = [
            FlowerType::class,
            TableType::class,
            BouquetType::class,
        ];

        $copied = 0; $missing = 0; $alreadyOnR2 = 0;

        foreach ($models as $modelClass) {
            $this->info(class_basename($modelClass) . ' işleniyor...');
            $rows = $modelClass::whereNotNull('image_path')->get();

            foreach ($rows as $row) {
                /** @var Model $row */
                $path = $row->image_path;

                // Skip if already on R2
                if ($r2->exists($path)) {
                    $alreadyOnR2++;
                    $this->line("  ⏭️  R2'de zaten var: $path");
                    continue;
                }

                if (!$source->exists($path)) {
                    $missing++;
                    $this->warn("  ⚠️  Kaynakta yok: $path");
                    continue;
                }

                $r2->put($path, $source->get($path));
                $copied++;
                $this->line("  ✅ $path");

                if ($this->option('delete')) {
                    $source->delete($path);
                }
            }
        }

        $this->newLine();
        $this->info("Toplam → Kopyalanan: $copied · R2'de zaten: $alreadyOnR2 · Eksik: $missing");
        return self::SUCCESS;
    }
}
