<?php

namespace Phone\Tests\Unit;

use Tests\TestCase;
use Phone\Models\Allocation;
use Phone\Models\PhoneNumber;
use Phone\Enums\AutoAllocation;
use Phone\Models\PhoneTransaction;

class PhoneNumberModelTest extends TestCase
{
    public function test_auto_allocation_suggesting()
    {
        $allocation = factory(Allocation::class)->create();
        $allocation2 = factory(Allocation::class)->create();

        /** @var PhoneNumber $number */
        $number = factory(PhoneNumber::class)->create(['auto_allocation' => AutoAllocation::AUTO_SUGGEST]);

        factory(PhoneTransaction::class)->create(['allocation_id' => $allocation2->id, 'phone_number_id' => $number->id]);
        factory(PhoneTransaction::class)->create(['allocation_id' => $allocation->id, 'phone_number_id' => $number->id]);
        factory(PhoneTransaction::class)->create();

        $number->loadSuggestedAllocation();

        $this->assertEquals($allocation->id, $number->suggested_allocation->id);
    }

    public function test_if_suggesting_only_on_auto_suggest_numbers()
    {
        $number = factory(PhoneNumber::class)->create(['auto_allocation' => AutoAllocation::MANUAL]);
        $number->loadSuggestedAllocation();
        $this->assertNull($number->suggested_allocation);
    }
}
