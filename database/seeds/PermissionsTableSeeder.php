<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'id'         => '1',
                'title'      => 'user_management_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '2',
                'title'      => 'permission_create',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '3',
                'title'      => 'permission_edit',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '4',
                'title'      => 'permission_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '5',
                'title'      => 'permission_delete',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '6',
                'title'      => 'permission_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '7',
                'title'      => 'role_create',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '8',
                'title'      => 'role_edit',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '9',
                'title'      => 'role_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '10',
                'title'      => 'role_delete',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '11',
                'title'      => 'role_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '12',
                'title'      => 'user_create',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '13',
                'title'      => 'user_edit',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '14',
                'title'      => 'user_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '15',
                'title'      => 'user_delete',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '16',
                'title'      => 'user_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '17',
                'title'      => 'account_create',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '18',
                'title'      => 'account_edit',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '19',
                'title'      => 'account_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '20',
                'title'      => 'account_delete',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '21',
                'title'      => 'account_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '22',
                'title'      => 'account_month_create',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '23',
                'title'      => 'account_month_edit',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '24',
                'title'      => 'account_month_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '25',
                'title'      => 'account_month_delete',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '26',
                'title'      => 'account_month_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '27',
                'title'      => 'transaction_create',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '28',
                'title'      => 'transaction_edit',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '29',
                'title'      => 'transaction_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '30',
                'title'      => 'transaction_delete',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '31',
                'title'      => 'transaction_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '32',
                'title'      => 'audit_log_show',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ],
            [
                'id'         => '33',
                'title'      => 'audit_log_access',
                'created_at' => '2019-05-14 18:24:54',
                'updated_at' => '2019-05-14 18:24:54',
            ], ];

        Permission::insert($permissions);
    }
}
