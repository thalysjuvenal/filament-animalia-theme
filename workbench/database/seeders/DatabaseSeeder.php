<?php

namespace Workbench\Database\Seeders;

use Illuminate\Database\Seeder;
use Workbench\App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Demo',
            'email' => 'demo@example.com',
            'password' => 'password',
        ]);

        foreach (range(1, 25) as $n) {
            $number = str_pad((string) $n, 2, '0', STR_PAD_LEFT);

            $user = new User([
                'name' => "Usuário {$number}",
                'email' => "user{$number}@example.com",
                'password' => 'password',
                'email_verified_at' => $n % 2 === 1 ? '2026-01-01 09:00:00' : null,
            ]);
            $user->created_at = '2026-01-01 09:00:00';
            $user->save();
        }
    }
}
