<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountMonthImport implements ToCollection
{
    public $rows;

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $this->rows = $rows;
    }
}
