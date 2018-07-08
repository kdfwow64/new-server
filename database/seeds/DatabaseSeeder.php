<?php

use Illuminate\Database\Seeder;
use database\seeds\EmailTemplateSeeder;
use database\seeds\PermissionSeeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(EmailTemplateSeeder::class);
    	$this->call(PermissionSeeder::class);
    }
}

