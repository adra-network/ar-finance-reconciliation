<?php

use Account\Models\Comment;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModalTypeToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->enum('modal_type', [Comment::MODAL_TRANSACTION, Comment::MODAL_RECONCILIATION]);
        });
    }
}
