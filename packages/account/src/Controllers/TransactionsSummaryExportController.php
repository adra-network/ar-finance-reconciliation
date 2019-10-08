<?php

namespace Account\Controllers;

use Account\Models\AccountImport;
use Carbon\Carbon;
use Account\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Account\Services\AccountPageExcelFileGeneratorService;
use PhpOffice\PhpSpreadsheet\Writer\Exception as SpreadsheetException;

class TransactionsSummaryExportController extends AccountBaseController
{
    /**
     * @param Request $request
     * @return mixed
     * @throws SpreadsheetException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function __invoke(Request $request)
    {
        $account = Account::findOrFail($request->input('account_id', null));
        $import = AccountImport::findOrFail($request->input('import', null));
        $generator = new AccountPageExcelFileGeneratorService($account, $import);

        if ($request->input('unallocated-only', null)) {
            $generator->unallocatedOnly();
        }

        $generator->generate();

        $sendEmail = $request->input('email', null);

        if (! is_null($sendEmail)) {
            $generator->saveFileTo(storage_path('app/exports'));
            Mail::raw('Transactions attached in email.', function ($message) use ($account, $generator) {
                $message->subject('Transactions of your account.');
                $message->from(config('mail.from.address'));
                $message->to($account->email);
                $message->attach(storage_path('app/exports/'.$generator->getFilename('.xlsx')));
            });

            return redirect()->route('account.transactions.summary', ['account_id' => $account->id, 'import' => $import])->withMessage(trans('global.export.email_sent_successfully'));
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$generator->getFilename().'.xlsx"');

            ob_end_clean();
            $generator->getWriter()->save('php://output');
            exit;
        }
    }
}
