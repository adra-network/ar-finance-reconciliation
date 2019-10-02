<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class CreateCommentTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('comment');
        });

        Artisan::call('db:seed', ['--class' => 'CommentTemplatesSeeder']);
    }
}
