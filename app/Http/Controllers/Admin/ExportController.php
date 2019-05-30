<?php

namespace App\Http\Controllers\Admin;

use App\AccountTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function accountExport(Request $request)
    {
        $account_id = $request->input('account_id', false);
        $selectedMonth = $request->input('month', false);
        $startDate = Carbon::parse($selectedMonth)->startOfMonth();
        $endDate = Carbon::parse($selectedMonth)->endOfMonth();

        $fileName = "export-".$account_id."-".$selectedMonth."-".now();

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
            $sheet->setCellValue('F' . $row, number_format($transaction->credit_amount, 2));
            $sheet->setCellValue('G' . $row, $transaction->comment);
            $row++;
        }

        $writer = new Xlsx($spreadSheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '.xlsx"');
        return $writer->save("php://output");
    }
}
