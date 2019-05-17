<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AccountMonthImport implements ToCollection
{
    public $data;

    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $this->data = $rows;
    }
}
