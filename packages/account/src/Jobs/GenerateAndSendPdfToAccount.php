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
    protected $shouldSendPdf;
    protected $transactionCount;

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

        $this->setShouldSendPdfAndTransactionCount();
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
        $statementTotal = number_format($this->account->getTotalTransactionsAmount(), 2);

        Mail::send('account::emails.pdf-mail', [
            'statementDate' => $statementDate,
            'accountUserName' => optional($this->account->user)->name,
            'statementTotal' => $statementTotal,
            'transactionCount' => $this->transactionCount,
        ],
            function ($message) use ($generator) {
                $message->subject('A/R Balance for: ' . $this->account->name);
                $message->from(config('mail.from.address'));
                $message->to($this->account->email);
                if ($this->shouldSendPdf) {
                    $message->attach(storage_path('app/exports/' . $generator->getFilename('.pdf')));
                }
            }
        );
    }

    // if statement total is 0
    // and there are no reconciliations
    // then we dont need to send the email
    private function setShouldSendPdfAndTransactionCount()
    {
        $statementTotal = number_format($this->account->getTotalTransactionsAmount(), 2);

        $lastImport = AccountImport::get()->last();
        $account = Account::with([
            'transactions' => function ($q) use ($lastImport) {
                $q->where('account_import_id', $lastImport->id);
            },
        ])->find($this->account->id);

        $this->transactionCount = $account->transactions->count();

        $hasReconciliations = $account->transactions->filter(
                function ($transaction) {
                    return $transaction->reconciliation_id !== null;
                }
            )->count() > 0;

        if ($statementTotal == 0 && !$hasReconciliations) {
            $this->shouldSendPdf = true;

            return false;
        }

        $this->shouldSendPdf = true;
    }
}
