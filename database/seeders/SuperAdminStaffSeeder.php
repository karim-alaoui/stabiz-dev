<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SuperAdminStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = 'superadmin@stabiz.com';
        $password = Hash::make('stabiz1234*stabiz');
        $data = [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => $email,
            'password' => $password,
        ];

        $staff = Staff::where('email', $email)->first();
        if ($staff) {
            $staff->password = $password;
            $staff->save();
        } else {
            $staff = Staff::create($data);
        }

        $superadmin = Staff::SUPER_ADMIN_ROLE;
        $role = Role::where('name', $superadmin)->first();
        if (!$role) {
            Role::create(['name' => $superadmin]);
        }

        if (!$staff->hasRole($superadmin)) {
            $staff->assignRole($superadmin);
        }
    }
}
