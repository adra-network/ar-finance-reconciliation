<?php

namespace Account\Jobs;

use Account\Models\Account;
use Account\Models\AccountImport;
use Account\Services\AccountPagePdfFileGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class GenerateAndSendPdfToAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $import;
    protected $account;

    /**
     * GenerateAndSendPdfToAccount constructor.
     * @param AccountImport $import
     * @param Account $account
     */
    public function __construct(AccountImport $import, Account $account)
    {
        $this->import = $import;
        $this->account = $account;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $generator = new AccountPagePdfFileGeneratorService($this->account, $this->import);
        $generator->generate();
        $generator->saveFileTo(storage_path('app/exports'));

        Mail::raw('Please find attached the personal account reconciliation statement for ' . $this->import->title . '.

Kind regards,
ADRA International Finance team',
            function ($message) use ($generator) {
                $message->subject('Personal Account Reconciliation for: ' . $this->account->name);
                $message->from(config('mail.from.address'));
                $message->to($this->account->email);
                $message->attach(storage_path('app/exports/' . $generator->getFilename('.pdf')));
            });
    }
}