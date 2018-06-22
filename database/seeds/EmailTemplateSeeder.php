<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
            'template_text'  	 => 'Hi, This is test email!',
			'template_text2'  	 => 'Hi, This is test email!',
        ]);
    }
}
