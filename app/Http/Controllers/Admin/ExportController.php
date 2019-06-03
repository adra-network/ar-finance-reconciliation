<?php

namespace App\Http\Controllers\Admin;

use App\Account;
use App\AccountTransaction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function accountTransactions(Request $request)
    {
        $account_id = $request->input('account_id', false);
        $selectedMonth = $request->input('month', false);
        $sendEmail = $request->input('email', false);
        $selectedAccount = Account::find($account_id);

        $startDate = Carbon::parse($selectedMonth)->startOfMonth();
        $endDate = Carbon::parse($selectedMonth)->endOfMonth();
        $exportDate = Carbon::now();
        $fileName = str_replace(":", "-", "export-" . $account_id . "-" . $selectedMonth . "-" . $exportDate->toDateString() . "-" . $exportDate->toTimeString());

        $spreadSheet = new Spreadsheet();
        $sheet = $spreadSheet->getActiveSheet();
        $sheet->setCellValue('A1', trans('global.transaction.fields.transaction_date'));
        $sheet->setCellValue('B1', trans('global.transaction.fields.transaction_id'));
        $sheet->setCellValue('C1', trans('global.transaction.fields.journal'));
        $sheet->setCellValue('D1', trans('global.transaction.fields.reference'));
        $sheet->setCellValue('E1', trans('global.transaction.fields.debit_amount'));
        $sheet->setCellValue('F1', trans('global.transaction.fields.credit_amount'));
        $sheet->setCellValue('G1', trans('global.transaction.fields.comment'));

        $accountTransactions = AccountTransaction::where('account_id', $account_id)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $row = 2;
        foreach ($accountTransactions as $transaction) {
            $sheet->setCellValue('A' . $row, date("m/d/Y", strtotime($transaction->transaction_date)));
            $sheet->setCellValue('B' . $row, $transaction->code);
            $sheet->setCellValue('C' . $row, $transaction->journal);
            $sheet->setCellValue('D' . $row, $transaction->reference);
            $sheet->setCellValue('E' . $row, number_format($transaction->debit_amount, 2));
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('F' . $row, number_format($transaction->credit_amount, 2));
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('0.00');
            $sheet->setCellValue('G' . $row, $transaction->comment);
            $row++;
        }

        $writer = new Xlsx($spreadSheet);
        if ($sendEmail) {
            $existsExportDir = \File::isDirectory(storage_path('app/exports/'));
            if(!$existsExportDir) {
               \File::makeDirectory(storage_path('app/exports/'));
            }
            $filenameToStore = storage_path('app/exports/') . $fileName . '.xlsx';
            $writer->save($filenameToStore);
            Mail::raw('Transactions attached in email.', function ($message) use ($selectedAccount, $filenameToStore) {
                $message->subject('Transactions of your account.');
                $message->from(Auth::user()->email);
                $message->to($selectedAccount->email);
                $message->attach($filenameToStore);
            });
            return redirect()->route('admin.account.transactions', ['account_id' => $account_id, 'month' => $selectedMonth])->withMessage(trans('global.export.email_sent_successfully'));
        } else {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx"');
            return $writer->save("php://output");
        }
    }
}
