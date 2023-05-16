<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            Staff::SUPER_ADMIN_ROLE,
            Staff::MATCH_MAKER_ROLE
        ];

        foreach ($roles as $role) {
            $exist = Role::where('name', $role)->first();
            if (!$exist) Role::create(['name' => $role]);
        }
    }
}
