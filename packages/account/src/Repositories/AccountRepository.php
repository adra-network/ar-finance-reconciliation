<?php

namespace Account\Repositories;

use Account\Models\AccountImport;
use Account\Models\MonthlySummary;
use Illuminate\Support\Collection;

class AccountRepository
{
    /**
     * @return Collection
     */
    public function getUnsyncedSummariesWithAccounts(): Collection
    {
        $lastImport = AccountImport::latest()->first();
        if (!$lastImport) {
            return collect();
        }

        return MonthlySummary::with('account.user')
            ->where('beginning_balance_in_sync', false)
            ->where('account_import_id', $lastImport->id)
            ->get();
    }
}
