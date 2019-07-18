<?php

namespace Account\Tests\Unit\DTO;

use Tests\TestCase;
use Account\DTO\TransactionReferenceIdData;

class TransactionReferenceIdDataTest extends TestCase
{
    public function test_toString_method()
    {
        $references = [
            //group TA1234 - 1
            'TA1234 Testing'           => 'TA1234',
            'TA1234AD Test Reference'  => 'TA1234',
            'Test TA1234AD Reference'  => 'TA1234',
            'Test TA1234 Reference'    => 'TA1234',
            'Test Reference TA1234'    => 'TA1234',
            'Test Reference TA1234ASD' => 'TA1234',
            //group TA12345 - 2
            'TA12345 Testing'           => 'TA12345',
            'TA12345AD Test Reference'  => 'TA12345',
            'Test TA12345AD Reference'  => 'TA12345',
            'Test TA12345 Reference'    => 'TA12345',
            'Test Reference TA12345'    => 'TA12345',
            'Test Reference TA12345ASD' => 'TA12345',
            //group TA2345 - 3
            'TA2345 Testing'           => 'TA2345',
            'TA2345AD Test Reference'  => 'TA2345',
            'Test TA2345AD Reference'  => 'TA2345',
            'Test TA2345 Reference'    => 'TA2345',
            'Test Reference TA2345'    => 'TA2345',
            'Test Reference TA2345ASD' => 'TA2345',
            //group TA23456 - 4
            'TA23456 Testing'           => 'TA23456',
            'TA23456AD Test Reference'  => 'TA23456',
            'Test TA23456AD Reference'  => 'TA23456',
            'Test TA23456 Reference'    => 'TA23456',
            'Test Reference TA23456'    => 'TA23456',
            'Test Reference TA23456ASD' => 'TA23456',
            //REVERSAL
            '<REVERSE> Test 1'  => 'reverse',
            '<REVERSAL> Test 2' => 'reverse',
            '<REVERSe> Test 3'  => 'reverse',
            '<REVERSal> Test 4' => 'reverse',
            '<reverse> Test 5'  => 'reverse',
            '<reversal> Test 6' => 'reverse',

            "December'18CC ADRA Travel: Hales CC Statement Exp" => 'DEC 18',
            "Hilda Madanat December '17 CC Canada Travel Expense" => 'DEC 17',
            "Nick DeFranco Dec'18 CC, Jan & October '19 Trips to Citiban Laurel" => 'DEC 18',
        ];

        foreach ($references as $key => $val) {
            $ref = TransactionReferenceIdData::make($key);
            $this->assertEquals($ref->toString(), $val);
        }
    }
}
