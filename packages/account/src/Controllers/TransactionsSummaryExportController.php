<?php

namespace Account\Controllers;

use Account\Models\Account;
use Account\Models\AccountImport;
use Account\Services\AccountPageExcelFileGeneratorService;
use Account\Services\AccountPagePdfFileGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Writer\Exception as SpreadsheetException;

class TransactionsSummaryExportController extends AccountBaseController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws SpreadsheetException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function __invoke(Request $request)
    {
        $account = Account::findOrFail($request->input('account_id', null));
        $import = AccountImport::findOrFail($request->input('import', null));
        $sendEmail = $request->input('email', null);

        if ($request->has('pdf')) {
            $generator = new AccountPagePdfFileGeneratorService($account, $import);
            $ext = '.pdf';
            if (is_null($sendEmail)) {
                return $generator->generate();
            }
        } else {
            $generator = new AccountPageExcelFileGeneratorService($account, $import);
            $ext = '.xlsx';
        }

        if ($request->input('unallocated-only', null)) {
            $generator->unallocatedOnly();
        }

        $generator->generate();

        if (!is_null($sendEmail)) {
            $generator->saveFileTo(storage_path('app/exports'));
            Mail::raw('Please find attached the personal account reconciliation statement for '.$import->title.'.

Kind regards,
ADRA International Finance team', function ($message) use ($account, $generator, $ext) {
                $message->subject('Personal Account Reconciliation for: ' . $account->name);
                $message->from(config('mail.from.address'));
                $message->to($account->email);
                $message->attach(storage_path('app/exports/' . $generator->getFilename($ext)));
            });

            return redirect()->route('account.transactions.summary', ['account_id' => $account->id, 'import' => $import])->withMessage(trans('global.export.email_sent_successfully'));
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $generator->getFilename() . $generator->getFilename($ext) . '"');

            ob_end_clean();
            $generator->getWriter()->save('php://output');
            exit;
        }
    }
}
