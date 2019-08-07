<?php

use Phone\Enums\AutoAllocation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToPhoneNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_numbers', function (Blueprint $table) {
            $table->unsignedBigInteger('allocation_id')->nullable();
            $table->text('comment')->nullable();
            $table->string('name')->nullable();
            $table->enum('auto_allocation', AutoAllocation::ENUM)->default('manual');
            $table->boolean('remember')->default(false);
        });
    }
}
