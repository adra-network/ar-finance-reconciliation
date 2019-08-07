<?php

use Phone\Models\Allocation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            $table->enum('charge_to', ['none', 'user', 'account']);
            $table->string('account_number')->nullable();
        });

        Allocation::create([
            'name' => 'Personal',
        ]);
        Allocation::create([
            'name' => 'Unsure',
        ]);
    }
}
