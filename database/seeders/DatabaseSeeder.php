<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = app(\App\Services\PermissionCatalog::class)->seedDefaults();
        $email = config('admin.email');
        $password = config('admin.password');

        if (is_string($email) && $email !== '' && is_string($password) && strlen($password) >= 12) {
            $user = User::query()->firstOrCreate(
                ['email' => $email],
                ['name' => config('admin.name'), 'password' => $password],
            );
            $user->roles()->syncWithoutDetaching([$roles['super-admin']->id]);
        }
    }
}
