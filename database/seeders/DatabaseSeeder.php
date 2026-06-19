<?php

namespace Database\Seeders;

use App\Models\CrmNotification;
use App\Models\Reward;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed the coffee shop menu catalog
        $this->call(MenuSeeder::class);

        // 1. Create Admin
        User::create([
            'name' => 'Admin CRM',
            'email' => 'admin@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 1b. Create Kasir
        User::create([
            'name' => 'Kasir Coffee',
            'email' => 'kasir@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'kasir',
        ]);

        // 2. Create Test Member (Rian)
        $rian = User::create([
            'name' => 'Rian Member',
            'email' => 'member@coffee.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'no_hp' => '081234567890',
            'tier_status' => 'Bronze',
            'total_poin' => 0,
            'total_pengeluaran' => 0,
        ]);

        // Give Rian some transactions (likes strong coffee, low sweetness, low average spending)
        $this->createTransactionsForUser($rian, 'Pecinta Kopi Strong & Hemat', 3);

        // 3. Create 45 Labeled Training Customers
        $categories = [
            [
                'label' => 'Pecinta Kopi Strong & Hemat',
                'names' => [
                    'Budi Santoso', 'Adi Wijaya', 'Hendra Kusuma', 'Rian Hidayat', 'Dedi Prasetyo',
                    'Agus Setiawan', 'Fajar Pratama', 'Joko Susilo', 'Rudi Hermawan', 'Iwan Kurniawan',
                    'Eko Prasetya', 'Bambang Utomo', 'Aris Munandar', 'Taufik Hidayat', 'Wawan Setiawan',
                ],
            ],
            [
                'label' => 'Pecinta Minuman Manis/Kekinian',
                'names' => [
                    'Siti Aminah', 'Dewi Lestari', 'Indah Permata', 'Rina Wulandari', 'Sari Indah',
                    'Mega Utami', 'Lia Novita', 'Fitri Handayani', 'Yuni Kartika', 'Dian Safitri',
                    'Anisa Rahma', 'Siska Amelia', 'Novianti', 'Putri Ayu', 'Ratih Purwasih',
                ],
            ],
            [
                'label' => 'Pelanggan Premium (Suka Es Krim/Kue Mahal)',
                'names' => [
                    'Christian Wibowo', 'Michael Chandra', 'Edward Hartono', 'David Wijaya', 'Richard Kevin',
                    'Jessica Veranda', 'Shania Junianatha', 'Melody Nurramdhani', 'Nabilah Ratna', 'Haruka Nakagawa',
                    'Gabriela Margareth', 'Devi Kinal', 'Rezky Wiranti', 'Sonia Natalia', 'Frieska Anastasia',
                ],
            ],
        ];

        foreach ($categories as $cat) {
            foreach ($cat['names'] as $index => $name) {
                // Generate a random email
                $email = strtolower(str_replace(' ', '.', $name)).'@example.com';

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'member',
                    'no_hp' => '08'.rand(10000000, 99999999),
                    'behavior_label' => $cat['label'],
                ]);

                // Create random transaction history (between 4 and 10 transactions)
                $txCount = rand(4, 10);
                $this->createTransactionsForUser($user, $cat['label'], $txCount);
            }
        }

        // Add some simulated notifications
        $this->seedNotificationLogs();

        // Seed reward catalog
        $this->seedRewards();
    }

    /**
     * Seed the reward catalog with sample items.
     */
    private function seedRewards(): void
    {
        $rewards = [
            ['nama' => 'Free Espresso Shot', 'deskripsi' => 'Tambahan 1 shot espresso gratis untuk minuman apa pun.', 'kategori' => 'Produk', 'poin_cost' => 500, 'stok' => null, 'icon' => '☕'],
            ['nama' => 'Voucher Diskon 25%', 'deskripsi' => 'Potongan 25% untuk satu kali transaksi.', 'kategori' => 'Voucher', 'poin_cost' => 1500, 'discount_percent' => 25, 'stok' => 100, 'icon' => '🎫'],
            ['nama' => 'Gratis Croissant Butter', 'deskripsi' => 'Tukar poin dengan 1 Croissant Butter renyah.', 'kategori' => 'Produk', 'poin_cost' => 2000, 'stok' => 50, 'icon' => '🥐'],
            ['nama' => 'Free Caffe Latte', 'deskripsi' => 'Nikmati 1 cangkir Caffe Latte gratis.', 'kategori' => 'Produk', 'poin_cost' => 2500, 'stok' => null, 'icon' => '🥛'],
            ['nama' => 'Cheesecake Premium', 'deskripsi' => 'Sepotong Cheesecake Premium istimewa.', 'kategori' => 'Produk', 'poin_cost' => 4000, 'stok' => 30, 'icon' => '🍰'],
            ['nama' => 'Tumbler Eksklusif Smart Coffee', 'deskripsi' => 'Tumbler edisi terbatas Smart Coffee CRM.', 'kategori' => 'Merchandise', 'poin_cost' => 8000, 'stok' => 20, 'icon' => '🥤'],
            ['nama' => 'Tote Bag Limited Edition', 'deskripsi' => 'Tote bag kanvas eksklusif untuk member setia.', 'kategori' => 'Merchandise', 'poin_cost' => 6000, 'stok' => 25, 'icon' => '👜'],
            ['nama' => 'Voucher Gratis Ongkir', 'deskripsi' => 'Gratis ongkos kirim untuk pesanan delivery.', 'kategori' => 'Voucher', 'poin_cost' => 1000, 'stok' => 200, 'icon' => '🚚'],
        ];

        foreach ($rewards as $reward) {
            Reward::create($reward);
        }
    }

    /**
     * Create realistic transactions matching the customer behavior profiles
     */
    private function createTransactionsForUser(User $user, string $label, int $txCount): void
    {
        // Define Menu Items Catalog
        $coffeeMenu = [
            ['nama' => 'Espresso', 'harga' => 18000, 'manis' => 1],
            ['nama' => 'Americano', 'harga' => 20000, 'manis' => 1],
            ['nama' => 'Caffe Latte', 'harga' => 25000, 'manis' => 2],
            ['nama' => 'Cappuccino', 'harga' => 25000, 'manis' => 2],
            ['nama' => 'Flat White', 'harga' => 24000, 'manis' => 1],
        ];

        $sweetMenu = [
            ['nama' => 'Kopi Susu Aren', 'harga' => 22000, 'manis' => 4],
            ['nama' => 'Matcha Latte', 'harga' => 28000, 'manis' => 4],
            ['nama' => 'Red Velvet Latte', 'harga' => 28000, 'manis' => 4],
            ['nama' => 'Caramel Macchiato', 'harga' => 29000, 'manis' => 5],
            ['nama' => 'Iced Lychee Tea', 'harga' => 22000, 'manis' => 5],
            ['nama' => 'Chocolate Signature', 'harga' => 26000, 'manis' => 4],
        ];

        $premiumMenu = [
            ['nama' => 'Croissant Butter', 'harga' => 30000, 'manis' => 2],
            ['nama' => 'Chocolate Fudge Cake', 'harga' => 35000, 'manis' => 4],
            ['nama' => 'Cheesecake Premium', 'harga' => 40000, 'manis' => 3],
            ['nama' => 'Almond Croissant', 'harga' => 32000, 'manis' => 3],
            ['nama' => 'Affogato', 'harga' => 30000, 'manis' => 4],
        ];

        $totalSpending = 0;
        $totalPoin = 0;

        for ($i = 0; $i < $txCount; $i++) {
            // Determine transaction date (staggered over the last 60 days)
            $date = Carbon::now()->subDays(rand(1, 60))->subHours(rand(1, 12));

            // Choose items based on behavior label
            $items = [];
            $itemCount = rand(1, 3);
            $txTotal = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $item = null;

                if ($label === 'Pecinta Kopi Strong & Hemat') {
                    // 85% coffee menu, 15% sweet menu
                    if (rand(1, 100) <= 85) {
                        $item = $coffeeMenu[array_rand($coffeeMenu)];
                    } else {
                        $item = $sweetMenu[array_rand($sweetMenu)];
                    }
                } elseif ($label === 'Pecinta Minuman Manis/Kekinian') {
                    // 85% sweet menu, 15% coffee menu
                    if (rand(1, 100) <= 85) {
                        $item = $sweetMenu[array_rand($sweetMenu)];
                    } else {
                        $item = $coffeeMenu[array_rand($coffeeMenu)];
                    }
                } else { // Pelanggan Premium (Suka Es Krim/Kue Mahal)
                    // High preference for food/pastry, mixed beverages
                    $rand = rand(1, 100);
                    if ($rand <= 50) {
                        $item = $premiumMenu[array_rand($premiumMenu)];
                    } elseif ($rand <= 80) {
                        $item = $coffeeMenu[array_rand($coffeeMenu)];
                    } else {
                        $item = $sweetMenu[array_rand($sweetMenu)];
                    }
                }

                $items[] = $item;
                $txTotal += $item['harga'];
            }

            // Determine tier status *before* adding transaction to calculate points
            $currentTier = 'Bronze';
            if ($totalSpending > 1500000) {
                $currentTier = 'Gold';
            } elseif ($totalSpending >= 500000) {
                $currentTier = 'Silver';
            }

            // Calculate points: 100 points per kelipatan Rp 10.000
            $basePoints = floor($txTotal / 10000) * 100;
            $multiplier = 1.0;
            if ($currentTier === 'Gold') {
                $multiplier = 1.5;
            } elseif ($currentTier === 'Silver') {
                $multiplier = 1.2;
            }
            $pointsEarned = (int) ($basePoints * $multiplier);

            // Create Transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'total_bayar' => $txTotal,
                'jumlah_item' => count($items),
                'created_at' => $date,
                'updated_at' => $date,
            ]);

            // Create Transaction Details
            foreach ($items as $itm) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'nama_menu' => $itm['nama'],
                    'kategori_menu' => in_array($itm, $premiumMenu) ? 'Food' : (in_array($itm, $coffeeMenu) ? 'Coffee' : 'Non-Coffee'),
                    'rasa_manis' => $itm['manis'],
                    'harga' => $itm['harga'],
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            $totalSpending += $txTotal;
            $totalPoin += $pointsEarned;
        }

        // Save total spending, points, and calculate final tier status
        $user->total_pengeluaran = $totalSpending;
        $user->total_poin = $totalPoin;

        if ($totalSpending > 1500000) {
            $user->tier_status = 'Gold';
        } elseif ($totalSpending >= 500000) {
            $user->tier_status = 'Silver';
        } else {
            $user->tier_status = 'Bronze';
        }

        $user->save();
    }

    /**
     * Seed initial notification logs for users
     */
    private function seedNotificationLogs(): void
    {
        $users = User::where('role', 'member')->limit(5)->get();
        foreach ($users as $user) {
            CrmNotification::create([
                'user_id' => $user->id,
                'type' => 'WhatsApp',
                'message' => "Halo {$user->name}, selamat! Anda terdaftar sebagai member di Smart Coffee CRM. Kumpulkan transaksi untuk naik ke Silver dan nikmati diskon 5%!",
                'created_at' => Carbon::now()->subDays(10),
            ]);

            CrmNotification::create([
                'user_id' => $user->id,
                'type' => 'Email',
                'message' => 'Selamat bergabung di Smart Coffee CRM! Dapatkan update promo personal terbaik Anda di dashboard.',
                'created_at' => Carbon::now()->subDays(10),
            ]);
        }
    }
}
