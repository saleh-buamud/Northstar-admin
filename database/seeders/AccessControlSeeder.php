<?php

namespace Database\Seeders;

use App\Services\PermissionCatalog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionCatalog::class)->seedDefaults();
    }
}
