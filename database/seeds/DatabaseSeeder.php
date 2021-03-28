<?php

use App\Laravue\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Laravue\Models\Role;
use App\Laravue\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

       $this->call([
            UsersTableSeeder::class,
            PermissionSeeder::class,
            PermissionGroupSeeder::class,
            cronProcessStatus::class
    ]);


    }
}
