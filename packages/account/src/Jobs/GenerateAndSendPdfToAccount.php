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
    protected $statementDate;

    /**
     * GenerateAndSendPdfToAccount constructor.
     * @param AccountImport $import
     * @param Account $account
     */
    public function __construct(AccountImport $import, Account $account, string $statementDate = null)
    {
        $this->import = $import;
        $this->account = $account;
        $this->statementDate = $statementDate;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $generator = new AccountPagePdfFileGeneratorService($this->account, $this->import, $this->statementDate);
        $generator->generate();
        $generator->saveFileTo(storage_path('app/exports'));

        $statementDate = $this->statementDate ?? $this->import->title;

        Mail::send('account::emails.pdf-mail', ['statementDate' => $statementDate],
            function ($message) use ($generator) {
                $message->subject('A/R Balance for: ' . $this->account->user->name);
                $message->from(config('mail.from.address'));
                $message->to($this->account->email);
                $message->attach(storage_path('app/exports/' . $generator->getFilename('.pdf')));
            });
    }
}
