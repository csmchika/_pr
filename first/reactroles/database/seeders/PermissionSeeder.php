<?php

namespace Database\Seeders;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $create = new Permission();
        $create->name = 'create';
        $create->slug = 'create';
        $create->save();
        $read = new Permission();
        $read->name = 'read';
        $read->slug = 'read';
        $read->save();
        $update = new Permission();
        $update->name = 'update';
        $update->slug = 'update';
        $update->save();
        $delete = new Permission();
        $delete->name = 'delete';
        $delete->slug = 'delete';
        $delete->save();
        $upload = new Permission();
        $upload->name = 'upload';
        $upload->slug = 'upload';
        $upload->save();
    }
}
