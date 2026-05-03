<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PosSeeder extends Seeder
{
    public function run(): void
    {
        // Truncate users table safely
        Schema::disableForeignKeyConstraints();
        DB::table('users')->delete();
        Schema::enableForeignKeyConstraints();

        // Only seed owner and one cashier
        $userData = [
            ['nama' => 'Owner MitraPOS', 'email' => 'owner@mitrapos.id', 'role' => User::ROLE_PEMILIK, 'status' => true],
            ['nama' => 'Nadia Putri', 'email' => 'kasir@mitrapos.id', 'role' => User::ROLE_KARYAWAN, 'status' => true],
        ];

        foreach ($userData as $index => $user) {
            User::query()->create([
                'nama' => $user['nama'],
                'email' => $user['email'],
                'password' => 'password',
                'role' => $user['role'],
                'status' => $user['status'],
                'email_verified_at' => now()->subDays($index),
            ]);
        }
    }
}
