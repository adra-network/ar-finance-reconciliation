<?php

namespace Account\Controllers;

use Account\Models\Account;
use App\Http\Controllers\Controller;
use Account\Services\AccountPageExcelFileGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Writer\Exception as SpreadsheetException;

class TransactionsSummaryExportController extends Controller
{
    /**
     * @param Request $request
     * @throws SpreadsheetException
     */
    public function __invoke(Request $request)
    {
        $account   = Account::findOrFail($request->input('account_id', null));
        $month     = Carbon::parse($request->input('month', null));
        $generator = new AccountPageExcelFileGeneratorService($account, $month);

        $sendEmail = $request->input('email', null);

        if (!is_null($sendEmail)) {
            $generator->saveFileTo(storage_path('app/exports'));
            Mail::raw('Transactions attached in email.', function ($message) use ($account, $generator) {
                $message->subject('Transactions of your account.');
                $message->from(config('mail.from.address'));
                $message->to($account->email);
                $message->attach(storage_path('app/exports/'.$generator->getFilename('.xlsx')));
            });

            return redirect()->route('account.transactions.summary', ['account_id' => $account->id, 'month' => $month->format('Y-m')])->withMessage(trans('global.export.email_sent_successfully'));
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="'.$generator->getFilename().'.xlsx"');

            return $generator->getWriter()->save('php://output');
        }
    }
}
