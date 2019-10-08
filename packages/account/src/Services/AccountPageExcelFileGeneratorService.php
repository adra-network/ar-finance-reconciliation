<?php

namespace Account\Services;

use Account\Models\Account;
use Account\Models\AccountImport;
use Account\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AccountPageExcelFileGeneratorService
{
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

    private $unallocatedOnly = false;

    private $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'P', 'O', 'R', 'S', 'T', 'U'];

    /**
     * AccountPageExcelFileGeneratorService constructor.
     * @param Account $account
     * @param AccountImport $import
     */
    public function __construct(Account $account, AccountImport $import)
    {
        $this->account = $account;

        $this->accountPageTableService = new AccountPageTableService($this->account, $import);

        $exportDate = now()->toTimeString();

        $this->filename = str_replace(':', '-', 'export-' . $account->id . '-' . $import->id . '-' . $exportDate);

        $this->spreadsheet = new Spreadsheet();

        $this->table1 = $this->accountPageTableService->getTable1();
        $this->table2 = $this->accountPageTableService->getTable2();
        $this->batchTable = (new BatchTableService())
            ->setClosingBalance($this->table1->monthlySummary->closing_balance ?? 0)
            ->showVariance()
            ->showOneAccount($account->id)
            ->getTableData();
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function generate(): void
    {
        if (!$this->unallocatedOnly) {
            $this->generateTable1();
            $this->generateTable2();
        }
        $this->generateBatchTable();
    }

    /**
     * @param bool $value
     */
    public function unallocatedOnly(bool $value = true): void
    {
        $this->unallocatedOnly = $value;
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
        return $this->filename . $append;
    }

    /**
     * @param string $dir
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function saveFileTo(string $dir): void
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0755);
        }
        $this->getWriter()->save($dir . DIRECTORY_SEPARATOR . $this->getFilename('.xlsx'));
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
     * @throws \PhpOffice\PhpSpreadsheet\Exception
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
            $sheet->setCellValue('A' . $this->r(true), Carbon::parse($transaction->transaction_date)->format('m/d/Y'));
            $sheet->setCellValue('B' . $this->r(), $transaction->code);
            $sheet->setCellValue('C' . $this->r(), $transaction->journal);
            $sheet->setCellValue('D' . $this->r(), $transaction->reference);
            $sheet->setCellValue('E' . $this->r(), number_format($transaction->debit_amount, 2));
            $sheet->getStyle('E' . $this->r())->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('F' . $this->r(), number_format($transaction->credit_amount, 2));
            $sheet->getStyle('F' . $this->r())->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('G' . $this->r(), $transaction->comment);
        }
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
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
        /** @var Transaction $transaction */
        foreach ($this->table2->transactions as $transaction) {
            $sheet->setCellValue('I' . $this->r(true), Carbon::parse($transaction->transaction_date)->format('m/d/Y'));
            $sheet->setCellValue('J' . $this->r(), $transaction->code);
            $sheet->setCellValue('K' . $this->r(), $transaction->reference);
            $sheet->setCellValue('L' . $this->r(), number_format($transaction->getCreditOrDebit(), 2));
            $sheet->setCellValue('M' . $this->r(), $transaction->comment);

            $sheet->getStyle('L' . $this->r())->getNumberFormat()->setFormatCode('0.00');
        }

        $sheet->setCellValue('K' . $this->r(true), 'Amount');
        $sheet->setCellValue('L' . $this->r(), number_format($this->table2->amount, 2));
        $sheet->getStyle('L' . $this->r())->getNumberFormat()->setFormatCode('0.00');

        $sheet->setCellValue('K' . $this->r(true), 'Variance');
        $sheet->setCellValue('L' . $this->r(), number_format($this->table2->variance, 2));
        $sheet->getStyle('L' . $this->r())->getNumberFormat()->setFormatCode('0.00');
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function generateBatchTable(): void
    {
        //OFFSET
        $o = -1;
        if (!$this->unallocatedOnly) {
            $o = 14;
        }

        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue($this->gr(0, $o) . '1', '');
        $sheet->setCellValue($this->gr(1, $o) . '1', 'Account');
        $sheet->setCellValue($this->gr(2, $o) . '1', 'Reconciled');
        $sheet->setCellValue($this->gr(3, $o) . '1', 'Date');
        $sheet->setCellValue($this->gr(4, $o) . '1', 'Transaction ID');
        $sheet->setCellValue($this->gr(5, $o) . '1', 'Reference');
        $sheet->setCellValue($this->gr(6, $o) . '1', 'Amount');

        $this->setRow(1);

        /** @var Account $account */
        /* @var Transaction $transaction */
        foreach ($this->batchTable->accounts as $account) {
            $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
            $sheet->setCellValue($this->gr(1, $o) . $this->r(), $account->name);
            $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(3, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(4, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(5, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(6, $o) . $this->r(), '');

            foreach ($account->getBatchTableReconciliations() as $reconciliation) {
                $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
                $sheet->setCellValue($this->gr(1, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(2, $o) . $this->r(), $reconciliation->uuid);
                $sheet->setCellValue($this->gr(3, $o) . $this->r(), $reconciliation->created_at->format('m/d/Y'));
                $sheet->setCellValue($this->gr(4, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(5, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(6, $o) . $this->r(), number_format($reconciliation->getTotalTransactionsAmount(), 2));

                $sheet->getStyle($this->gr(6, $o) . $this->r())->getNumberFormat()->setFormatCode('0.00');

                foreach ($reconciliation->transactions as $transaction) {
                    $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
                    $sheet->setCellValue($this->gr(1, $o) . $this->r(), '');
                    $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
                    $sheet->setCellValue($this->gr(3, $o) . $this->r(), $transaction->transaction_date);
                    $sheet->setCellValue($this->gr(4, $o) . $this->r(), $transaction->code);
                    $sheet->setCellValue($this->gr(5, $o) . $this->r(), $transaction->reference);
                    $sheet->setCellValue($this->gr(6, $o) . $this->r(), number_format($transaction->getCreditOrDebit(), 2));
                    $sheet->getStyle($this->gr(6, $o) . $this->r())->getNumberFormat()->setFormatCode('0.00');
                }
            }

            foreach ($account->getUnallocatedTransactionGroups() as $reference_id => $transactions) {
                $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
                $sheet->setCellValue($this->gr(1, $o) . $this->r(), 'Auto' . $reference_id);
                $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(3, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(4, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(5, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(6, $o) . $this->r(), '');

                foreach ($transactions as $transaction) {
                    $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
                    $sheet->setCellValue($this->gr(1, $o) . $this->r(), '');
                    $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
                    $sheet->setCellValue($this->gr(3, $o) . $this->r(), $transaction->transaction_date);
                    $sheet->setCellValue($this->gr(4, $o) . $this->r(), $transaction->code);
                    $sheet->setCellValue($this->gr(5, $o) . $this->r(), $transaction->reference);
                    $sheet->setCellValue($this->gr(6, $o) . $this->r(), number_format($transaction->getCreditOrDebit(), 2));
                    $sheet->getStyle($this->gr(6, $o) . $this->r())->getNumberFormat()->setFormatCode('0.00');
                }
            }

            $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
            $sheet->setCellValue($this->gr(1, $o) . $this->r(), 'Un-Reconciled');
            $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(3, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(4, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(5, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(6, $o) . $this->r(), '');

            foreach ($account->getUnallocatedTransactionsWithoutGrouping() as $transaction) {
                $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
                $sheet->setCellValue($this->gr(1, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
                $sheet->setCellValue($this->gr(3, $o) . $this->r(), $transaction->transaction_date);
                $sheet->setCellValue($this->gr(4, $o) . $this->r(), $transaction->code);
                $sheet->setCellValue($this->gr(5, $o) . $this->r(), $transaction->reference);
                $sheet->setCellValue($this->gr(6, $o) . $this->r(), number_format($transaction->getCreditOrDebit(), 2));
                $sheet->getStyle($this->gr(6, $o) . $this->r())->getNumberFormat()->setFormatCode('0.00');
            }

            $sheet->setCellValue($this->gr(0, $o) . $this->r(true), '');
            $sheet->setCellValue($this->gr(1, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(2, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(3, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(4, $o) . $this->r(), '');
            $sheet->setCellValue($this->gr(5, $o) . $this->r(), 'Closing balance');
            $sheet->setCellValue($this->gr(6, $o) . $this->r(), number_format($account->getTotalTransactionsAmount(), 2));
            $sheet->getStyle($this->gr(6, $o) . $this->r())->getNumberFormat()->setFormatCode('0.00');
        }
    }

    /**
     * function to get a row from $rows with a conditional offset.
     * @param $row
     * @param int $offset
     * @return mixed
     */
    private function gr($row, $offset = 0)
    {
        $row = $row + $offset;
        $row = $row >= 0 ? $row : 0;

        return $this->rows[$row];
    }
}
