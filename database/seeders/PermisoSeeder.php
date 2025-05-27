<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Enums\PermisosEnum;
use Spatie\Permission\Models\Permission;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermisosEnum::cases() as $per) {
            Permission::firstOrCreate(['name' => $per->value, 'guard_name' => 'api']);
        }
    }
}
