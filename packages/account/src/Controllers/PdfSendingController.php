<?php


namespace Account\Controllers;

use Account\Jobs\GenerateAndSendPdfToAccount;
use Account\Models\Account;
use Account\Models\AccountImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PdfSendingController
{

    public function index()
    {
        return view('account::send-pdfs.index', [
            'imports'  => AccountImport::latest('date_from')->get(),
            'accounts' => Account::whereNotNull('email')->get(),
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'statement_date' => 'required',
            'accounts'  => function ($attribute, $value, $fail) {
                $ids = array_keys($value);

                $count = Account::whereIn('id', $ids)->count();

                if ($count !== count($ids)) {
                    $fail($attribute . ' are invalid.');
                }
            },
        ]);


        $accounts = Account::whereIn('id', array_keys($data['accounts']))->get();
        $import = AccountImport::latest()->first();

        foreach($accounts as $account) {
            dispatch(new GenerateAndSendPdfToAccount($import, $account, $request->input('statement_date')));
        }

        return back()->with('message', "PDFs sent successfully");
    }
}
