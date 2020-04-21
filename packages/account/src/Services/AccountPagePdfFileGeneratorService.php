<?php


namespace Account\Services;


use Account\Models\Account;
use Account\Models\AccountImport;
use Account\TransactionAlertSystem\Intervals;
use Barryvdh\DomPDF\PDF;

class AccountPagePdfFileGeneratorService
{

    /** @var Account */
    private $account;
    /** @var AccountImport */
    private $import;
    /** @var bool */
    private $unallocatedOnly = true;
    /** @var string */
    private $filename;
    /** @var PDF */
    private $pdf;
    /** @var object */
    private $batchTable;

    /**
     * AccountPagePdfFileGeneratorService constructor.
     * @param Account $account
     * @param AccountImport $import
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(Account $account, AccountImport $import)
    {
        if (PHP_VERSION > 7.3) {
            error_reporting(E_ALL ^ E_DEPRECATED);
        }

        $this->account = $account;
        $this->import = $import;


        $exportDate = now()->toTimeString();
        $this->filename = str_replace(':', '-', 'export-' . $account->id . '-' . $import->id . '-' . $exportDate);

        $this->pdf = app()->make('dompdf.wrapper');

        $accountPageTableService = new AccountPageTableService($this->account, $this->import);
        $table1 = $accountPageTableService->getTable1();

        $this->batchTable = (new BatchTableService())
            ->setClosingBalance($table1->monthlySummary->closing_balance ?? 0)
            ->showOneAccount($account->id)
            ->getTableData(true);

    }

    /**
     * @param bool $value
     */
    public function unallocatedOnly(bool $value = true): void
    {
        $this->unallocatedOnly = $value;
    }

    /**
     *
     */
    public function generate()
    {
        $this->pdf->loadHTML(view('account::pdf.accountPagePdf', [
            'account' => $this->account,
            'batchTable' => $this->batchTable,
            'intervals' => new Intervals(),
            'import' => $this->import,
        ]));

        return $this->pdf->stream();
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
     */
    public function saveFileTo(string $dir): void
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0755);
        }
        $this->pdf->save($dir . DIRECTORY_SEPARATOR . $this->getFilename('.pdf'));
    }

}
