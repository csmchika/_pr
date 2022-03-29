<?php

namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = new Role();
        $superadmin->name = 'superadmin';
        $superadmin->slug = 'superadmin';
        $superadmin->save();
        $admin = new Role();
        $admin->name = 'admin';
        $admin->slug = 'admin';
        $admin->save();
        $redactor = new Role();
        $redactor->name = 'redactor';
        $redactor->slug = 'redactor';
        $redactor->save();
        $user = new Role();
        $user->name = 'user';
        $user->slug = 'user';
        $user->save();
        $guest = new Role();
        $guest->name = 'guest';
        $guest->slug = 'guest';
        $guest->save();

    }
}
