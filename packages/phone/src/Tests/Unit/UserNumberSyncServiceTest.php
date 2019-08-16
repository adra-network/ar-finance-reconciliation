<?php

namespace Phone\Tests\Unit;

use App\User;
use Tests\TestCase;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;
use Phone\Services\UserNumberSyncService;

class UserNumberSyncServiceTest extends TestCase
{
    public function test_syncAccountNumbers_method()
    {
        $user = User::find(1);

        $service = new UserNumberSyncService($user);

        $accountNumber1 = factory(AccountPhoneNumber::class)->create(['user_id' => null]);
        $accountNumber2 = factory(AccountPhoneNumber::class)->create(['user_id' => null]);

        $callerNumber1 = factory(CallerPhoneNumber::class)->create(['user_id' => null]);
        $callerNumber2 = factory(CallerPhoneNumber::class)->create(['user_id' => null]);
        $callerNumber3 = factory(CallerPhoneNumber::class)->create(['user_id' => null]);
        $callerNumber4 = factory(CallerPhoneNumber::class)->create(['user_id' => null]);

        $transaction1 = factory(PhoneTransaction::class)->create([
            'account_phone_number_id' => $accountNumber1->id,
            'caller_phone_number_id' => $callerNumber1->id,
        ]);
        $transaction2 = factory(PhoneTransaction::class)->create([
            'account_phone_number_id' => $accountNumber1->id,
            'caller_phone_number_id' => $callerNumber2->id,
        ]);
        $transaction3 = factory(PhoneTransaction::class)->create([
            'account_phone_number_id' => $accountNumber2->id,
            'caller_phone_number_id' => $callerNumber3->id,
        ]);
        $transaction4 = factory(PhoneTransaction::class)->create([
            'account_phone_number_id' => $accountNumber2->id,
            'caller_phone_number_id' => $callerNumber4->id,
        ]);

        $service->syncAccountNumbers([$accountNumber1->id, $accountNumber2->id]);

        $user = User::with('accountPhoneNumbers', 'callerPhoneNumbers')->find(1);

        $this->assertCount(2, $user->accountPhoneNumbers);
        $this->assertCount(4, $user->callerPhoneNumbers);
    }
}
