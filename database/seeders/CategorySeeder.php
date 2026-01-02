<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Ekonomi',
            'Kesehatan',
            'Pendidikan',
            'Infrastruktur',
            'Demografi',
            'Lingkungan',
            'Pertanian',
            'Perdagangan',
            'Pariwisata',
            'Transportasi',
            'Industri',
            'Teknologi Informasi',
            'Keuangan',
            'Keamanan',
            'Sosial Budaya',
        ];

        foreach ($names as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}
