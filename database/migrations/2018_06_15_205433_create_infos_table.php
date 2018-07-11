<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('option')->unsigned()->nullable();
            $table->integer('opt_out')->unsigned()->nullable();
            $table->string('business_name')->nullable();
            $table->string('domain_name')->nullable();
        //    $table->integer('rank')->unsigned()->nullable();
            $table->string('admins_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mailing_address')->nullable();
            $table->integer('flag')->unsigned()->nullable();
            $table->integer('black')->unsigned()->nullable();
        //    $table->integer('warning_total')->unsigned()->nullable();
        //    $table->integer('error_total')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *gitonline@awsgitonline.aws.synapsegroupinc.com:repos/online/microservices/mat.gitgitonline@awsgitonline.aws.synapsegroupinc.com:repos/online/microservices/mat.git
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infos');
    }
}
