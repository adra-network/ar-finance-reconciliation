<?php


namespace Account\Services;


use Account\Models\Account;
use Account\Models\AccountImport;

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

    /**
     * AccountPagePdfFileGeneratorService constructor.
     * @param Account $account
     * @param AccountImport $import
     */
    public function __construct(Account $account, AccountImport $import)
    {
        $this->account = $account;
        $this->import = $import;


        $exportDate = now()->toTimeString();
        $this->filename = str_replace(':', '-', 'export-' . $account->id . '-' . $import->id . '-' . $exportDate);
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
    public function generate(): void
    {
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML(view('accounts::pdf.accountPagePdf', [
            'account' => $this->account
        ]));

        $pdf->save(storage_path('app/exports/' . $this->filename));
    }

}