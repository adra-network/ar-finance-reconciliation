<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhoneTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phone_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('phone_number_id');
            $table->foreign('phone_number_id')->references('id')->on('phone_numbers');
            $table->string('section_id')->nullable();
            $table->integer('foundation_account_number')->nullable();
            $table->string('foundation_account_name')->nullable();
            $table->bigInteger('billing_account_number')->nullable();
            $table->string('billing_account_name')->nullable();
            $table->bigInteger('wireless_number')->nullable();
            $table->date('market_cycle_end_date')->nullable();
            $table->integer('item')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('rate_code')->nullable();
            $table->string('rate_period')->nullable();
            $table->string('feature')->nullable();
            $table->string('type_code')->nullable();
            $table->string('legend')->nullable();
            $table->string('voice_data_indicator')->nullable();
            $table->string('roaming_indicator')->nullable();
            $table->float('total_charges')->nullable();
            $table->string('originating_location')->nullable();
            $table->bigInteger('number_called_to_from')->nullable();
            $table->string('voice_called_to')->nullable();
            $table->string('voice_in_out')->nullable();
            $table->integer('minutes_used')->nullable();
            $table->integer('airtime_charge')->nullable();
            $table->float('ld_add_charge')->nullable();
            $table->float('intl_tax')->nullable();
            $table->string('day')->nullable();
            $table->string('data_to_from')->nullable();
            $table->string('data_type')->nullable();
            $table->string('data_in_out')->nullable();
            $table->integer('data_usage_amount')->nullable();
            $table->string('data_usage_measure')->nullable();
            $table->string('video_share_rate_code')->nullable();
            $table->bigInteger('video_share_to_from')->nullable();
            $table->string('video_share_in_out')->nullable();
            $table->float('video_share_domestic_usage_charges')->nullable();
            $table->integer('video_share_domestic_minutes')->nullable();
            $table->string('video_share_international_roaming_location')->nullable();
            $table->float('video_share_international_roaming_charges')->nullable();
            $table->integer('video_share_international_roaming_minutes')->nullable();
            $table->bigInteger('vehicle_identification_number')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('trim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('phone_transactions');
    }
}
