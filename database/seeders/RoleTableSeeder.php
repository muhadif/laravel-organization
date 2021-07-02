<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $role = Role::create(['name' => 'Account Manager']);
        $permissions = Permission::whereIn('id', [9, 12, 13, 14, 15, 16])->pluck('id', 'id');
        $role->syncPermissions($permissions);

        $role = Role::create(['name' => 'User']);
        $permissions = Permission::whereIn('id', [9, 13])->pluck('id', 'id');
        $role->syncPermissions($permissions);


    }
}
