<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Muhammad Adi Febri',
            'email' => 'admin@muhadif.com',
            'phone' => '08126781723',
            'password' => bcrypt('12345678')
        ]);

        $role = Role::create(['name' => 'Admin']);

        $permissions = Permission::all()->pluck('id', 'id');

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);
    }
}
