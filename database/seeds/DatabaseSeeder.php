<?php

use App\Account;
use App\AccountTransaction;
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
            $account = factory(Account::class)->create();
            factory(AccountTransaction::class, 20)->create([
                'account_id' => $account->id,
            ]);
        }
    }
}
