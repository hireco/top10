<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->increments('id');

            $table->string('brief',10);
            $table->string('affiliated',20);
            $table->string('title',20);
            $table->Integer('online');
            $table->Integer('popularity');
            $table->string('logo',50);
            $table->string('domain_name',30);
            $table->string('site_url',50);
			$table->string('color',30);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('forums');
    }
}
