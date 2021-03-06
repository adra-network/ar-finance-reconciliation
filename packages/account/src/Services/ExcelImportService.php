<?php

namespace Account\Services;

use Carbon\Carbon;
use Account\Models\Account;
use Account\DTO\AccountData;
use Account\Models\Transaction;
use Account\Models\AccountImport;
use Account\Models\MonthlySummary;
use Illuminate\Support\Collection;
use Account\DTO\AccountTransactionData;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;

class ExcelImportService
{
    /**
     * @param string $filename
     *
     * @return Collection
     * @throws \Exception
     */
    public function parseMonthlyReportOfAccounts(string $filename): Collection
    {
        if (! file_exists($filename)) {
            throw new \Exception('File '.$filename.' does not exit.');
        }

        $array = explode('.', strtolower($filename));
        $extension = end($array);

        if ($extension == 'xls' || $extension == 'xlsx') {
            $reader = new Xls();
        } else {
            $reader = new Csv();
        }

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
            if ($transactionsOnThisRow && $cells['A']->value != 'Account:' && $cells['C']->value != '') {
                $transaction = new AccountTransactionData();
                $transaction->date = Carbon::parse($cells['A']->formatedValue);
                $transaction->code = $cells['C']->value;
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
                $account->bebinningBalanceDate = Carbon::parse($cells['A']->formatedValue);
                $account->beginningBalance = $cells['M']->value;
                //if we find a new balance then next row will be empty and the row after that will be beginining of transactions
                $transactionsOnNextRow = true;
            }
            if ($cells['I']->value === 'Account Net Change') {
                $account->netChange = $cells['M']->value;
            }
            if ($cells['I']->value === 'Account Ending Balance') {
                $account->endingBalanceDate = $cells['A']->value;
                $account->endingBalance = $cells['M']->value;
                $accounts->push($account);
            }
        }

        return $accounts;
    }

    /**
     * @param Collection $accounts
     * @param AccountImport $accountImport
     */
    public function saveParsedDataToDatabase(Collection $accounts, AccountImport $accountImport): void
    {
        $maxDateTo = null;
        $minDateFrom = null;
        /* @var AccountData $account */
        foreach ($accounts as $accountData) {
            $account = Account::firstOrCreate(['code' => $accountData->code, 'name' => $accountData->name]);

            $bb = Carbon::parse($accountData->bebinningBalanceDate);
            $minDateFrom = ! is_null($minDateFrom) && $bb->gt($minDateFrom) ? $minDateFrom : $bb;

            $eb = Carbon::parse($accountData->endingBalanceDate);
            $maxDateTo = ! is_null($minDateFrom) && $bb->lt($maxDateTo) ? $maxDateTo : $eb;

            //create a summary
            /** @var MonthlySummary $summary */
            $summary = $account->monthlySummaries()->firstOrCreate([
                'date_from' => Carbon::parse($accountData->bebinningBalanceDate)->format('Y-m-d'),
                'date_to' => Carbon::parse($accountData->endingBalanceDate)->format('Y-m-d'),
            ], [
                'month_date' => Carbon::parse($accountData->endingBalanceDate)->format('Y-m-d'),
                'beginning_balance' => $accountData->beginningBalance,
                'ending_balance' => $accountData->endingBalance,
                'net_change' => $accountData->netChange,
                'account_import_id' => $accountImport->id,
            ]);

            $summary->checkSummaryBeginningBalance();

            /** @var AccountTransactionData $transaction */
            foreach ($accountData->transactions as $transaction) {
                $account->transactions()->firstOrCreate([
                    'code' => $transaction->code,
                ], [
                    'journal' => $transaction->journal,
                    'reference' => $transaction->reference,
                    'debit_amount' => $transaction->debit,
                    'credit_amount' => $transaction->credit,
                    'transaction_date' => $transaction->date->format('Y-m-d'),
                    'account_import_id' => $accountImport->id,
                ]);
            }
        }

        $accountImport->date_from = $minDateFrom;
        $accountImport->date_to = $maxDateTo;
        $accountImport->save();
    }

    /**
     * @param Row $row
     *
     * @return array
     */
    private function getCellValues(Row $row): array
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

            if (substr($val, 0, 1) == '$' || substr($val, 0, 2) == '($') {
                $fval = $val = $this->parseCSVNumber($val);
            }

            $cells[$cell->getColumn()] = (object) [
                'value' => $val ?? null,
                'formatedValue' => $fval ?? null,
            ];
        }

        return $cells;
    }

    private function parseCSVNumber($value)
    {
        if (substr($value, 0, 2) == '($') {
            $value = str_replace('(', '', $value);
            $value = str_replace(')', '', $value);
            $value = '-'.$value;
        }

        $numberFormatter = new \NumberFormatter('en-US', \NumberFormatter::CURRENCY);

        return numfmt_parse_currency($numberFormatter, $value, $currency);
    }
}
