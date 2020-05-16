<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',15)->unique();
            $table->string('tid',15);
			
            $table->text('content');

            $table->string('username',25);
            $table->string('nickname',50);

            $table->string('category');
            $table->string('forum',10);
            $table->timestamp('post_time');

            $table->integer('hits')->unsigned()->default(0);
            $table->integer('support')->unsigned()->default(0);
            $table->integer('oppose')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('replies');
    }
}
