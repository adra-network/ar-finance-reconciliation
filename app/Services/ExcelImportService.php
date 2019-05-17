<?php

namespace App\Services;

use App\Imports\AccountMonthImport;
use Maatwebsite\Excel\Facades\Excel;

class ExcelImportService {

    public function import_account_month($filename)
    {
        $account_month_import = new AccountMonthImport();
        Excel::import($account_month_import, $filename);

        return $account_month_import->data;
    }

}