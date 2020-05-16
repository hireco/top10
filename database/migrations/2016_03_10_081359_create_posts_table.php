<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid',15)->unique();

            $table->string('title',100);
            $table->text('content');

            $table->string('username',25);
            $table->string('nickname',50);

            $table->string('category');
            $table->string('forum',10);
            $table->timestamp('post_time');
			$table->timestamp('top_time');

            $table->integer('replies')->unsigned()->default(0);

            $table->integer('hits')->unsigned()->default(0);

            $table->string('original_url',200);

            $table->string('emotion',60);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
    }
}
