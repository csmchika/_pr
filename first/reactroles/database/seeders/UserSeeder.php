<?php

namespace Database\Seeders;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::where('slug','superadmin')->first();
        $redactor = Role::where('slug', 'redactor')->first();
        $admin = Role::where('slug', 'admin')->first();
        $user = Role::where('slug', 'user')->first();
        $createTasks = Permission::where('slug','create')->first();
        $readTasks = Permission::where('slug','read')->first();
        $updateTasks = Permission::where('slug','update')->first();
        $deleteTasks = Permission::where('slug','delete')->first();
        $uploadTasks = Permission::where('slug','upload')->first();
        $user1 = new User();
        $user1->name = 'Jhon Deo';
        $user1->email = 'jhon@deo.com';
        $user1->password = bcrypt('secret');
        $user1->save();
        $user1->roles()->attach($superadmin);
        $user1->permissions()->attach($deleteTasks);
        $user2 = new User();
        $user2->name = 'Mike Thomas';
        $user2->email = 'mike@thomas.com';
        $user2->password = bcrypt('secret');
        $user2->save();
        $user2->roles()->attach($redactor);
        $user2->permissions()->attach($createTasks);
        $user2->permissions()->attach($readTasks);
        $user3 = new User();
        $user3->name = 'Role Permission';
        $user3->email = 'Role@perm.com';
        $user3->password = bcrypt('secret');
        $user3->save();
        $user3->roles()->attach($user);
        $user3->permissions()->attach($readTasks);
    }
}
