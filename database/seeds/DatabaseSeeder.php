<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
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

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = \App\Model\MailTemplate::create([
            'template_name'      => 'Sample',
            'template_text'      => '<p>Hi</p>',
            'template_text2'     => 'Hi',
        ]);
    }
}

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Model\Permission::create([
            'permission'      => 'mailing',
            'value'      => 1,
        ]);
    }
}

