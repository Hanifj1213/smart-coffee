<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Seed the default coffee shop menu.
     */
    public function run(): void
    {
        $menus = [
            ['nama' => 'Espresso', 'harga' => 18000, 'kategori' => 'Coffee', 'rasa_manis' => 1, 'icon' => '🥃', 'image' => '/images/menu/espresso.png'],
            ['nama' => 'Americano', 'harga' => 20000, 'kategori' => 'Coffee', 'rasa_manis' => 1, 'icon' => '☕', 'image' => '/images/menu/americano.png'],
            ['nama' => 'Caffe Latte', 'harga' => 25000, 'kategori' => 'Coffee', 'rasa_manis' => 2, 'icon' => '🥛', 'image' => '/images/menu/caffe_latte.png'],
            ['nama' => 'Cappuccino', 'harga' => 25000, 'kategori' => 'Coffee', 'rasa_manis' => 2, 'icon' => '☕', 'image' => '/images/menu/cappuccino.png'],
            ['nama' => 'Flat White', 'harga' => 24000, 'kategori' => 'Coffee', 'rasa_manis' => 1, 'icon' => '🥛', 'image' => '/images/menu/flat_white.png'],
            ['nama' => 'Kopi Susu Aren', 'harga' => 22000, 'kategori' => 'Non-Coffee', 'rasa_manis' => 4, 'icon' => '🍯', 'image' => '/images/menu/kopi_susu_aren.png'],
            ['nama' => 'Matcha Latte', 'harga' => 28000, 'kategori' => 'Non-Coffee', 'rasa_manis' => 4, 'icon' => '🍵', 'image' => '/images/menu/matcha_latte.png'],
            ['nama' => 'Red Velvet Latte', 'harga' => 28000, 'kategori' => 'Non-Coffee', 'rasa_manis' => 4, 'icon' => '🍰', 'image' => '/images/menu/red_velvet_latte.png'],
            ['nama' => 'Chocolate Signature', 'harga' => 26000, 'kategori' => 'Non-Coffee', 'rasa_manis' => 4, 'icon' => '🍫', 'image' => '/images/menu/chocolate_signature.png'],
            ['nama' => 'Caramel Macchiato', 'harga' => 29000, 'kategori' => 'Non-Coffee', 'rasa_manis' => 5, 'icon' => '🍮', 'image' => '/images/menu/caramel_macchiato.png'],
            ['nama' => 'Iced Lychee Tea', 'harga' => 22000, 'kategori' => 'Non-Coffee', 'rasa_manis' => 5, 'icon' => '🧃', 'image' => '/images/menu/iced_lychee_tea.png'],
            ['nama' => 'Croissant Butter', 'harga' => 30000, 'kategori' => 'Food', 'rasa_manis' => 2, 'icon' => '🥐', 'image' => '/images/menu/croissant_butter.png'],
            ['nama' => 'Chocolate Fudge Cake', 'harga' => 35000, 'kategori' => 'Food', 'rasa_manis' => 4, 'icon' => '🍰', 'image' => '/images/menu/chocolate_fudge_cake.png'],
            ['nama' => 'Cheesecake Premium', 'harga' => 40000, 'kategori' => 'Food', 'rasa_manis' => 3, 'icon' => '🧀', 'image' => '/images/menu/cheesecake_premium.png'],
            ['nama' => 'Almond Croissant', 'harga' => 32000, 'kategori' => 'Food', 'rasa_manis' => 3, 'icon' => '🥐', 'image' => '/images/menu/almond_croissant.png'],
            ['nama' => 'Affogato', 'harga' => 30000, 'kategori' => 'Food', 'rasa_manis' => 4, 'icon' => '🍨', 'image' => '/images/menu/affogato.png'],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['nama' => $menu['nama']],
                $menu + ['is_active' => true],
            );
        }
    }
}
