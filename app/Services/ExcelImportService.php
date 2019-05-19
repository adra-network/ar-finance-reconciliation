<?php

namespace App\Services;

use App\Account;
use App\Imports\AccountMonthImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportService {

    public function import_account_month($filename)
    {
        $account_month_import = new AccountMonthImport();
        if (!file_exists($filename)) {
            return [];
        }
        Excel::import($account_month_import, $filename);
        $rows = $account_month_import->rows;

        $accounts = [];

        foreach ($rows as $row) {
            // @TODO implementation
        }

        /*
         * Expected result format of this data analysis - array of accounts with their data
         *
         * [
                '01-1-0-00-0-0-000-14565' => [
                    'name' => '01-1-0-00-0-0-000-14565 (A/R - Alfredo)',
                    'beginning_balance' => 8731.08,
                    'transactions' => [
                        [
                            'date' => '2019-04-30',
                            'transaction_id' => '87006-42',
                            'journal' => 'Journal Entry',
                            'reference' => 'Alfredo April'19 CC Statement Expenses',
                            'debit' => 150,
                            'credit' => 0,
                        ]
                    ],
                    'net_change' => 150,
                    'ending_balance' => 8881.08,
                ],
                '01-1-0-00-0-0-000-14627' => [
                    'name' => '01-1-0-00-0-0-000-14627 (A/R - Miscellaneous Employee)',
                    'beginning_balance' => 4356.48,
                    'transactions' => [
                        [
                            'date' => '2019-04-01',
                            'transaction_id' => '87155-1',
                            'journal' => 'Journal Entry',
                            'reference' => 'TA1267AD Trav Adv to Brazil',
                            'debit' => 0,
                            'credit' => 50,
                        ],
                        [
                            'date' => '2019-04-01',
                            'transaction_id' => '87155-3',
                            'journal' => 'Journal Entry',
                            'reference' => 'TA1413 Billy Andre Tunisia',
                            'debit' => 0,
                            'credit' => 1000,
                        ],
                        // ... more transactions
                    ],
                    'net_change' => -4356.48,
                    'ending_balance' => 0,
                ],
            ]
         *
         * */

        return $accounts;

    }

    public function save_accounts_and_transactions($accounts)
    {
        foreach ($accounts as $account_code => $account_data) {
            Account::firstOrCreate(['code' => $account_code, 'name' => $account_data['name']]);
        }

        // @TODO Save Account Months

        // @TODO Save Transactions
    }

}