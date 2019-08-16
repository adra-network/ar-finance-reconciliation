<?php

namespace Phone\Tests\Unit;

use Tests\TestCase;
use Phone\Models\Allocation;
use Phone\Enums\AutoAllocation;
use Phone\Models\PhoneTransaction;
use Phone\Models\CallerPhoneNumber;
use Phone\Models\AccountPhoneNumber;

class PhoneNumberModelTest extends TestCase
{
    public function test_auto_allocation_suggesting()
    {
        $allocation = factory(Allocation::class)->create();
        $allocation2 = factory(Allocation::class)->create();

        $accountPhoneNumber = factory(AccountPhoneNumber::class)->create();

        /** @var CallerPhoneNumber $number */
        $number = factory(CallerPhoneNumber::class)->create(['auto_allocation' => AutoAllocation::AUTO_SUGGEST]);
        $number2 = factory(CallerPhoneNumber::class)->create(['auto_allocation' => AutoAllocation::AUTO_SUGGEST]);

        factory(PhoneTransaction::class)->create(['allocation_id' => $allocation2->id, 'caller_phone_number_id' => $number->id, 'account_phone_number_id' => $accountPhoneNumber->id]);
        factory(PhoneTransaction::class)->create(['allocation_id' => $allocation->id, 'caller_phone_number_id' => $number2->id, 'account_phone_number_id' => $accountPhoneNumber->id]);
        factory(PhoneTransaction::class)->create();

        $number->loadSuggestedAllocation();

        $this->assertEquals($allocation->id, $number->suggested_allocation->id);
    }

    public function test_if_suggesting_only_on_auto_suggest_numbers()
    {
        $number = factory(CallerPhoneNumber::class)->create(['auto_allocation' => AutoAllocation::MANUAL]);
        $number->loadSuggestedAllocation();
        $this->assertNull($number->suggested_allocation);
    }
}
