<?php

namespace Database\Seeders;

use App\Models\FlowerType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FlowerTypeSeeder extends Seeder
{
    public function run(): void
    {
        $flowers = [
            ['Antoryum Kırmızı', 'kırmızı', 'adet'],
            ['Yeşil Antoryum', 'yeşil', 'adet'],
            ['Ammi Visnaga', 'beyaz', 'adet'],
            ['Karanfil Kelle Pembe', 'pembe', 'adet'],
            ['Karanfil Kelle Sarı', 'sarı', 'adet'],
            ['Karanfil Kelle Yeşil', 'yeşil', 'adet'],
            ['Sarı Lisianthus', 'sarı', 'adet'],
            ['Mor Lisianthus', 'mor', 'adet'],
            ['Pembe Lisianthus', 'pembe', 'adet'],
            ['Koyu Mavi Delphinium', 'koyu mavi', 'adet'],
            ['Pembe Delphinium', 'pembe', 'adet'],
            ['Beyaz Delphinium', 'beyaz', 'adet'],
            ['Katmerli Bebe Mavisi Delphinium', 'bebe mavisi', 'adet'],
            ['Sarı Anastasia', 'sarı', 'adet'],
            ['Yeşil Anastasia', 'yeşil', 'adet'],
            ['Statis Pembe', 'pembe', 'adet'],
            ['Statis Koyu Pembe', 'koyu pembe', 'adet'],
            ['Statis Sarı', 'sarı', 'adet'],
            ['Statis Mor', 'mor', 'adet'],
            ['Statis Beyaz', 'beyaz', 'adet'],
            ['Beyaz Karanfil Sprey', 'beyaz', 'adet'],
            ['Sarı Karanfil Sprey', 'sarı', 'adet'],
            ['Lepidium', 'yeşil', 'adet'],
            ['Pembe Fışfırık', 'pembe', 'adet'],
            ['Soli', 'yeşil', 'adet'],
            ['Pembe Gerbera', 'pembe', 'adet'],
            ['Somon Pembe Gerbera', 'somon', 'adet'],
            ['Turuncu Gerbera', 'turuncu', 'adet'],
            ['Pembe Şebboy', 'pembe', 'adet'],
            ['Lila Şebboy', 'lila', 'adet'],
            ['Somon Şebboy', 'somon', 'adet'],
            ['Yosun', '—', 'çuval'],
        ];

        foreach ($flowers as [$name, $color, $unit]) {
            FlowerType::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'color' => $color,
                    'unit' => $unit,
                ]
            );
        }
    }
}
