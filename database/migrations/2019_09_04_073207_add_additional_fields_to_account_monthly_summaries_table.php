<?php

use Carbon\Carbon;
use Account\Models\Transaction;
use Account\Models\AccountImport;
use Account\Models\MonthlySummary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdditionalFieldsToAccountMonthlySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::beginTransaction();

        try {

            //rename summaries table
            Schema::rename('account_monthly_summaries', 'account_period_summaries');

            //create account_imports table
            Schema::create('account_imports', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id');
                $table->string('title');
                $table->string('filename');
                $table->date('date_from')->nullable();
                $table->date('date_to')->nullable();
                $table->timestamps();
            });

            //gets import dates
            $imports = MonthlySummary::select('created_at')->groupBy('created_at')->get()->pluck('created_at')->toArray();
            $uniqueImports = [];
            foreach ($imports as $import) {
                $import = Carbon::parse($import)->format('Y-m-d');
                if (! in_array($import, $uniqueImports)) {
                    $uniqueImports[] = $import;
                }
            }

            //creates account imports by got dates
            $accounts = [];
            foreach ($uniqueImports as $importDate) {
                $accounts[$importDate] = AccountImport::create([
                    'user_id' => 1,
                    'title' => 'Auto-generated: '.Carbon::parse($importDate)->format('m/d/Y'),
                    'filename' => 'fake',
                    'date_from' => Carbon::parse($importDate)->startOfMonth(),
                    'date_to' => Carbon::parse($importDate)->endOfMonth(),
                ]);
            }

            //create fields for account_imports
            Schema::table('account_period_summaries', function (Blueprint $table) {
                $table->bigInteger('account_import_id')->nullable();
                $table->date('date_from')->nullable();
                $table->date('date_to')->nullable();
            });

            //add account_imports to summaries
            $monthlySummaries = MonthlySummary::get();
            $monthlySummaries->each(function ($summary) use ($accounts) {
                $md = Carbon::createFromFormat(config('panel.date_format'), $summary->month_date)->format('Y-m-d');
                $summary->date_from = $md;
                $summary->date_to = $md;
                $summary->account_import_id = $accounts[$summary->created_at->format('Y-m-d')]->id;
                $summary->save();
            });

            //make summaries new fields non nullable how they should be
            Schema::table('account_period_summaries', function (Blueprint $table) {
                $table->bigInteger('account_import_id')->nullable(false)->change();
                $table->date('date_from')->nullable(false)->change();
                $table->date('date_to')->nullable(false)->change();
            });

            //add account imports to transactions
            Schema::table('account_transactions', function (Blueprint $table) {
                $table->bigInteger('account_import_id')->nullable();
            });

            /** @var MonthlySummary $monthlySummary */
            foreach ($monthlySummaries as $monthlySummary) {
                Transaction::query()
                    ->where('account_id', $monthlySummary->account_id)
                    ->where('created_at', $monthlySummary->created_at)
                    ->update(['account_import_id' => $monthlySummary->account_import_id]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
