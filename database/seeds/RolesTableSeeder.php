<?php

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        
        //User Admin
        $user = User::find(1); //Italo Morales
        $user->assignRole('Guest');

        $role = Role::find(2);
        $role->givePermissionTo('products.edit');

     }   
}
