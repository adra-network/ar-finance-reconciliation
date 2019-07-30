<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePhoneTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_transactions', function(Blueprint $table) {
            $table->bigInteger('wireless_number')->nullable()->change();
            $table->float('total_charges')->nullable()->change();
            $table->bigInteger('number_called_to_from')->nullable()->change();
            $table->string('voice_data_indicator')->nullable()->change();
            $table->string('data_to_from')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
