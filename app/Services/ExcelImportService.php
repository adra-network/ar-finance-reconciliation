<?php

namespace App\Services;

use App\Account;
use App\DTO\AccountData;
use App\DTO\AccountTransactionData;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class ExcelImportService
{

    /**
     * @param string $filename
     * @return Collection
     * @throws \Exception
     */
    public function parseMonthlyReportOfAccounts(string $filename): Collection
    {
        if (!file_exists($filename)) {
            throw new \Exception('File ' . $filename . ' does not exit.');
        }

        $reader = new Xls();
        $spreadsheet = $reader->load($filename);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->getRowIterator();

        $accounts = collect([]);

        $account = new AccountData();
        $transactionsOnNextRow = false;
        $transactionsOnThisRow = false;
        foreach ($rows as $row) {
            $cells = $this->getCellValues($row);
            //READING ROWS AND STATUS WITH USING CONTINUE HERE!!

            //if transactions are on the next row skipp this one, cause its empty
            if ($transactionsOnNextRow) {
                $transactionsOnNextRow = false;
                $transactionsOnThisRow = true;
                continue;
            }
            //if we reach subtotals then the transactions have ended
            if ($cells['I']->value === 'Account Subtotals' && $transactionsOnThisRow) {
                $transactionsOnThisRow = false;
            }
            //if transactions on this row then add new transaction to account
            if ($transactionsOnThisRow) {
                $transaction = new AccountTransactionData();
                $transaction->date = Carbon::parse($cells['A']->formatedValue);
                $transaction->id = $cells['C']->value;
                $transaction->journal = $cells['D']->value;
                $transaction->reference = $cells['G']->value;
                $transaction->debit = $cells['K']->value;
                $transaction->credit = $cells['L']->value;
                $account->transactions->push($transaction);
            }

            //READ REGULAR ROWS WITHOUT USING CONTINUE HERE!!

            if ($cells['A']->value === 'Account:') {
                $account = new AccountData();
                $key = $cells['B']->value;
                $key = explode(' ', $key);
                $key = $key[0];

                $account->code = $key;
                $account->name = $cells['B']->value;
            }
            if ($cells['I']->value === 'Account Beginning Balance') {
                $account->beginningBalance = $cells['M']->value;
                //if we find a new balance then next row will be empty and the row after that will be beginining of transactions
                $transactionsOnNextRow = true;
            }
            if ($cells['I']->value === 'Account Net Change') {
                $account->netChange = $cells['M']->value;
            }
            if ($cells['I']->value === 'Account Ending Balance') {
                $account->endingBalance = $cells['M']->value;
                $accounts->push($account);
            }

        }

        return $accounts;

    }

    /**
     * @param Collection $accounts
     */
    public function save_accounts_and_transactions(Collection $accounts) : void
    {
        /** @var AccountData $account */
        foreach ($accounts as $accountData) {
            Account::firstOrCreate(['code' => $accountData->code, 'name' => $accountData->name]);
        }

        // @TODO Save Account Months

        // @TODO Save Transactions
    }

    /**
     * @param $row
     * @return array
     */
    private function getCellValues($row)
    {
        $cells = [];
        /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
        foreach ($row->getCellIterator() as $cell) {
            //Value will be formated by php (i think) and say we have a date (1/3/2019) then php will try to do the calculation and output some number
            //Formatted value will be formated by excel library first and then returned, so if we have the same date, we till get a date string
            //There is a catch with numbers. Say we have a money field that in execel looks like this -$US(xxxx) then if we get a formatted value from lib,
            //it will return $US(xxx) as a string without the minus sign, and if we try to get a simple value, we get a propper (float) number with a minus sign.
            $val = $cell->getValue();
            $fval = $cell->getFormattedValue();
            $cells[$cell->getColumn()] = (object)[
                'value'         => $val ?? null,
                'formatedValue' => $fval ?? null,
            ];
        }

        return $cells;
    }

}