<?php

namespace App\Services;

use App\Account;
use App\AccountTransaction;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AccountPageExcelFileGeneratorService
{
    /** @var CarbonInterface */
    private $monthStart;

    /** @var CarbonInterface */
    private $monthEnd;

    /** @var Account */
    private $account;

    /** @var string */
    private $filename;

    /** @var Spreadsheet */
    private $spreadsheet;

    /** @var Xlsx */
    private $writer;

    /** @var AccountPageTableService */
    private $accountPageTableService;

    /** @var int */
    private $row = 0;

    /** @var object */
    private $table1;

    /** @var object */
    private $table2;

    /** @var object */
    private $batchTable;

    /**
     * AccountPageExcelFileGeneratorService constructor.
     *
     * @param Account         $account
     * @param CarbonInterface $month
     */
    public function __construct(Account $account, CarbonInterface $month)
    {
        $this->account = $account;
        $this->monthStart = $month->copy()->startOfMonth();
        $this->monthEnd = $month->copy()->endOfMonth();

        $this->accountPageTableService = new AccountPageTableService($this->account, $month);

        $exportDate = now()->toTimeString();

        $this->filename = str_replace(':', '-', 'export-'.$account->id.'-'.$month.'-'.$exportDate.'-'.$exportDate);

        $this->spreadsheet = new Spreadsheet();

        $this->table1 = $this->accountPageTableService->getTable1();
        $this->table2 = $this->accountPageTableService->getTable2();
        $this->batchTable = (new BatchTableService())
            ->setClosingBalance($this->table1->monthlySummary->closing_balance ?? 0)
            ->showVariance()
            ->showOneAccount($account->id)
            ->getTableData();

        $this->generateTable1();
        $this->generateTable2();
        $this->generateBatchTable();
    }

    /**
     * @param bool $new
     *
     * @return Xlsx
     */
    public function getWriter($new = false): Xlsx
    {
        if (!isset($this->writer) || $new) {
            $this->writer = new Xlsx($this->spreadsheet);
        }

        return $this->writer;
    }

    /**
     * @param string $append
     *
     * @return string
     */
    public function getFilename(string $append = ''): string
    {
        return $this->filename.$append;
    }

    /**
     * @param string $dir
     */
    public function saveFileTo(string $dir): void
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0755);
        }
        $this->getWriter()->save($dir.DIRECTORY_SEPARATOR.$this->getFilename('.xlsx'));
    }

    /**
     * Get current row and increment.
     *
     * @param bool $increment
     *
     * @return int
     */
    private function r(bool $increment = false): int
    {
        return $increment ? ++$this->row : $this->row;
    }

    /**
     * @param int $row
     */
    private function setRow($row = 0): void
    {
        $this->row = $row;
    }

    /**
     * @return void
     */
    private function generateTable1(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', trans('global.transaction.fields.transaction_date'));
        $sheet->setCellValue('B1', trans('global.transaction.fields.transaction_id'));
        $sheet->setCellValue('C1', trans('global.transaction.fields.journal'));
        $sheet->setCellValue('D1', trans('global.transaction.fields.reference'));
        $sheet->setCellValue('E1', trans('global.transaction.fields.debit_amount'));
        $sheet->setCellValue('F1', trans('global.transaction.fields.credit_amount'));
        $sheet->setCellValue('G1', trans('global.transaction.fields.comment'));

        foreach ($this->table1->transactions as $transaction) {
            $sheet->setCellValue('A'.$this->r(true), Carbon::parse($transaction->transaction_date)->format('m/d/Y'));
            $sheet->setCellValue('B'.$this->r(), $transaction->code);
            $sheet->setCellValue('C'.$this->r(), $transaction->journal);
            $sheet->setCellValue('D'.$this->r(), $transaction->reference);
            $sheet->setCellValue('E'.$this->r(), number_format($transaction->debit_amount, 2));
            $sheet->getStyle('E'.$this->r())->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('F'.$this->r(), number_format($transaction->credit_amount, 2));
            $sheet->getStyle('F'.$this->r())->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('G'.$this->r(), $transaction->comment);
        }
    }

    /**
     * @return void
     */
    private function generateTable2(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('I1', 'Date');
        $sheet->setCellValue('J1', 'Transaction ID');
        $sheet->setCellValue('K1', 'Reference');
        $sheet->setCellValue('L1', 'Amount');
        $sheet->setCellValue('M1', 'Comment');

        $this->setRow(1);
        /** @var AccountTransaction $transaction */
        foreach ($this->table2->transactions as $transaction) {
            $sheet->setCellValue('I'.$this->r(true), Carbon::parse($transaction->transaction_date)->format('m/d/Y'));
            $sheet->setCellValue('J'.$this->r(), $transaction->code);
            $sheet->setCellValue('K'.$this->r(), $transaction->reference);
            $sheet->setCellValue('L'.$this->r(), number_format($transaction->getCreditOrDebit(), 2));
            $sheet->setCellValue('M'.$this->r(), $transaction->comment);

            $sheet->getStyle('L'.$this->r())->getNumberFormat()->setFormatCode('0.00');
        }

        $sheet->setCellValue('K'.$this->r(true), 'Amount');
        $sheet->setCellValue('L'.$this->r(), number_format($this->table2->amount, 2));
        $sheet->getStyle('L'.$this->r())->getNumberFormat()->setFormatCode('0.00');

        $sheet->setCellValue('K'.$this->r(true), 'Variance');
        $sheet->setCellValue('L'.$this->r(), number_format($this->table2->variance, 2));
        $sheet->getStyle('L'.$this->r())->getNumberFormat()->setFormatCode('0.00');
    }

    /**
     * @return void
     */
    private function generateBatchTable(): void
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('N1', '');
        $sheet->setCellValue('O1', 'Account');
        $sheet->setCellValue('P1', 'Reconciled');
        $sheet->setCellValue('Q1', 'Date');
        $sheet->setCellValue('R1', 'Transaction ID');
        $sheet->setCellValue('S1', 'Reference');
        $sheet->setCellValue('T1', 'Amount');
        $sheet->setCellValue('U1', 'Comment');

        $this->setRow(1);

        /** @var Account $account */
        /* @var AccountTransaction $transaction */
        foreach ($this->batchTable->accounts as $account) {
            $sheet->setCellValue('N'.$this->r(true), '');
            $sheet->setCellValue('O'.$this->r(), $account->name);
            $sheet->setCellValue('P'.$this->r(), '');
            $sheet->setCellValue('Q'.$this->r(), '');
            $sheet->setCellValue('R'.$this->r(), '');
            $sheet->setCellValue('S'.$this->r(), '');
            $sheet->setCellValue('T'.$this->r(), '');
            $sheet->setCellValue('U'.$this->r(), '');

            foreach ($account->getBatchTableReconciliations() as $reconciliation) {
                $sheet->setCellValue('N'.$this->r(true), '');
                $sheet->setCellValue('O'.$this->r(), '');
                $sheet->setCellValue('P'.$this->r(), $reconciliation->uuid);
                $sheet->setCellValue('Q'.$this->r(), $reconciliation->created_at->format('m/d/Y'));
                $sheet->setCellValue('R'.$this->r(), '');
                $sheet->setCellValue('S'.$this->r(), '');
                $sheet->setCellValue('T'.$this->r(), number_format($reconciliation->getTotalTransactionsAmount(), 2));
                $sheet->setCellValue('U'.$this->r(), $reconciliation->comment);

                $sheet->getStyle('T'.$this->r())->getNumberFormat()->setFormatCode('0.00');

                foreach ($reconciliation->transactions as $transaction) {
                    $sheet->setCellValue('N'.$this->r(true), '');
                    $sheet->setCellValue('O'.$this->r(), '');
                    $sheet->setCellValue('P'.$this->r(), '');
                    $sheet->setCellValue('Q'.$this->r(), $transaction->transaction_date);
                    $sheet->setCellValue('R'.$this->r(), $transaction->code);
                    $sheet->setCellValue('S'.$this->r(), $transaction->reference);
                    $sheet->setCellValue('T'.$this->r(), number_format($transaction->getCreditOrDebit(), 2));
                    $sheet->setCellValue('U'.$this->r(), $transaction->comment);
                    $sheet->getStyle('T'.$this->r())->getNumberFormat()->setFormatCode('0.00');
                }
            }

            foreach ($account->getUnallocatedTransactionGroups() as $reference_id => $transactions) {
                $sheet->setCellValue('N'.$this->r(true), '');
                $sheet->setCellValue('O'.$this->r(), 'Auto'.$reference_id);
                $sheet->setCellValue('P'.$this->r(), '');
                $sheet->setCellValue('Q'.$this->r(), '');
                $sheet->setCellValue('R'.$this->r(), '');
                $sheet->setCellValue('S'.$this->r(), '');
                $sheet->setCellValue('T'.$this->r(), '');
                $sheet->setCellValue('U'.$this->r(), '');

                foreach ($transactions as $transaction) {
                    $sheet->setCellValue('N'.$this->r(true), '');
                    $sheet->setCellValue('O'.$this->r(), '');
                    $sheet->setCellValue('P'.$this->r(), '');
                    $sheet->setCellValue('Q'.$this->r(), $transaction->transaction_date);
                    $sheet->setCellValue('R'.$this->r(), $transaction->code);
                    $sheet->setCellValue('S'.$this->r(), $transaction->reference);
                    $sheet->setCellValue('T'.$this->r(), number_format($transaction->getCreditOrDebit(), 2));
                    $sheet->setCellValue('U'.$this->r(), $transaction->comment);
                    $sheet->getStyle('T'.$this->r())->getNumberFormat()->setFormatCode('0.00');
                }
            }

            $sheet->setCellValue('N'.$this->r(true), '');
            $sheet->setCellValue('O'.$this->r(), 'Un-Allocated');
            $sheet->setCellValue('P'.$this->r(), '');
            $sheet->setCellValue('Q'.$this->r(), '');
            $sheet->setCellValue('R'.$this->r(), '');
            $sheet->setCellValue('S'.$this->r(), '');
            $sheet->setCellValue('T'.$this->r(), '');
            $sheet->setCellValue('U'.$this->r(), '');

            foreach ($account->getUnallocatedTransactionsWithoutGrouping() as $transaction) {
                $sheet->setCellValue('N'.$this->r(true), '');
                $sheet->setCellValue('O'.$this->r(), '');
                $sheet->setCellValue('P'.$this->r(), '');
                $sheet->setCellValue('Q'.$this->r(), $transaction->transaction_date);
                $sheet->setCellValue('R'.$this->r(), $transaction->code);
                $sheet->setCellValue('S'.$this->r(), $transaction->reference);
                $sheet->setCellValue('T'.$this->r(), number_format($transaction->getCreditOrDebit(), 2));
                $sheet->setCellValue('U'.$this->r(), $transaction->comment);
                $sheet->getStyle('T'.$this->r())->getNumberFormat()->setFormatCode('0.00');
            }

            $sheet->setCellValue('N'.$this->r(true), '');
            $sheet->setCellValue('O'.$this->r(), '');
            $sheet->setCellValue('P'.$this->r(), '');
            $sheet->setCellValue('Q'.$this->r(), '');
            $sheet->setCellValue('R'.$this->r(), '');
            $sheet->setCellValue('S'.$this->r(), '');
            $sheet->setCellValue('T'.$this->r(), 'Closing balance');
            $sheet->setCellValue('U'.$this->r(), number_format($account->getTotalTransactionsAmount(), 2));
            $sheet->getStyle('U'.$this->r())->getNumberFormat()->setFormatCode('0.00');

            $sheet->setCellValue('N'.$this->r(true), '');
            $sheet->setCellValue('O'.$this->r(), '');
            $sheet->setCellValue('P'.$this->r(), '');
            $sheet->setCellValue('Q'.$this->r(), '');
            $sheet->setCellValue('R'.$this->r(), '');
            $sheet->setCellValue('S'.$this->r(), '');
            $sheet->setCellValue('T'.$this->r(), 'Variance');
            $sheet->setCellValue('U'.$this->r(), number_format($account->getVariance(), 2));
            $sheet->getStyle('U'.$this->r())->getNumberFormat()->setFormatCode('0.00');
        }
    }
}
