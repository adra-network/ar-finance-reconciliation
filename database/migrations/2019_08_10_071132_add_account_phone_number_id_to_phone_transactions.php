<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccountPhoneNumberIdToPhoneTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('phone_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('caller_phone_number_id')->nullable();
            $table->unsignedBigInteger('account_phone_number_id')->nullable();
            $table->dropForeign('phone_transactions_phone_number_id_foreign');
            $table->dropIndex('phone_transactions_phone_number_id_foreign');
            $table->dropColumn('phone_number_id');
        });
    }
}
