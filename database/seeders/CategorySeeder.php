<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Tenda',
                'deskripsi' => 'Peralatan untuk tempat berlindung saat camping'
            ],
            [
                'nama_kategori' => 'Carrier',
                'deskripsi' => 'Tas untuk membawa perlengkapan camping'
            ],
            [
                'nama_kategori' => 'Sleeping Bag',
                'deskripsi' => 'Peralatan tidur saat kegiatan camping'
            ],
            [
                'nama_kategori' => 'Peralatan Masak',
                'deskripsi' => 'Peralatan untuk memasak selama camping'
            ],
            [
                'nama_kategori' => 'Penerangan',
                'deskripsi' => 'Peralatan penerangan saat camping'
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['nama_kategori' => $category['nama_kategori']],
                $category
            );
        }
    }
}
