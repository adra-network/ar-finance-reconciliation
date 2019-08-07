<?php

namespace Phone\Enums;

class ChargeTo
{
    const NONE = 'none';
    const USER = 'user';
    const ACCOUNT = 'account';

    const ENUM = [
        self::NONE,
        self::USER,
        self::ACCOUNT,
    ];
}
