<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTransactionsImportSyncData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Updating the data that was not set historically while importing
        \DB::statement("update account_transactions set account_import_id = 7 where account_import_id is null and date(created_at) = '2019-10-14'");
        \DB::statement("update account_transactions set account_import_id = 8 where account_import_id is null and date(created_at) = '2019-10-15'");
        \DB::statement("update account_transactions set account_import_id = 10 where account_import_id is null and date(created_at) = '2019-11-19'");
        \DB::statement("update account_transactions set account_import_id = 11 where account_import_id is null and date(created_at) = '2019-12-17'");
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
