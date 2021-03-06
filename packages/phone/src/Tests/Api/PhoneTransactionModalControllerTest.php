<?php

namespace Phone\Tests\Api;

use App\User;
use Tests\TestCase;
use Phone\Models\Allocation;
use Phone\Enums\AutoAllocation;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;

class PhoneTransactionModalControllerTest extends TestCase
{
    public function test_save_phone_number_dialog()
    {
        $phoneNumber = factory(CallerPhoneNumber::class)->create();
        $phoneTransaction = factory(PhoneTransaction::class)->create();
        $allocation = factory(Allocation::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('phone.transaction-modal.save'), [
            'phoneNumber' => [
                'id' => $phoneNumber->id,
                'auto_allocation' => AutoAllocation::AUTO_SUGGEST,
                'name' => 'testname',
                'phone_number' => 'testnumber',
                'remember' => true,
                'comment' => 'testcomment',
                'allocation_id' => $allocation->id,
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('caller_phone_numbers', [
            'id' => $phoneNumber->id,
            'auto_allocation' => AutoAllocation::AUTO_SUGGEST,
            'name' => 'testname',
            'phone_number' => 'testnumber',
            'remember' => true,
            'comment' => 'testcomment',
            'allocation_id' => $allocation->id,
        ]);
    }

    public function test_save_phone_transaction_dialog()
    {
        $phoneNumber = factory(CallerPhoneNumber::class)->create();
        $phoneTransaction = factory(PhoneTransaction::class)->create([
            'caller_phone_number_id' => $phoneNumber->id,
            'allocation_id' => null,
            'comment' => null,
        ]);
        $allocation = factory(Allocation::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->post(route('phone.transaction-modal.save'), [
            'phoneNumber' => [
                'id' => $phoneNumber->id,
                'auto_allocation' => AutoAllocation::AUTO_SUGGEST,
                'name' => 'testname',
                'phone_number' => 'testnumber',
                'remember' => true,
                'comment' => 'testcomment',
                'allocation_id' => $allocation->id,
            ],
            'transaction' => [
                'id' => $phoneTransaction->id,
                'comment' => 'testcomment2',
                'allocation_id' => $allocation->id,
            ],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('caller_phone_numbers', [
            'id' => $phoneNumber->id,
            'auto_allocation' => AutoAllocation::AUTO_SUGGEST,
            'name' => 'testname',
            'phone_number' => 'testnumber',
            'remember' => true,
            'comment' => null,
            'allocation_id' => $phoneNumber->allocation_id,
        ]);
        $this->assertDatabaseHas('phone_transactions', [
            'id' => $phoneTransaction->id,
            'comment' => 'testcomment2',
            'allocation_id' => $allocation->id,
        ]);
    }
}
