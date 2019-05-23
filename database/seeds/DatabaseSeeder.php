<?php

use App\Account;
use App\AccountTransaction;
use App\Reconciliation;
use App\Services\ReconciliationService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
        ]);


        if (env('APP_ENV') == 'local') {
            $this->call(LocalSeeder::class);
        }
    }
}
