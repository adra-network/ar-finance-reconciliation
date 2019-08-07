<?php

namespace Phone\Enums;

class AutoAllocation
{
    const MANUAL = 'manual';
    const AUTO_ALLOCATE = 'auto-allocate';
    const AUTO_SUGGEST = 'auto-suggest';

    const ENUM = [
        self::MANUAL,
        self::AUTO_ALLOCATE,
        self::AUTO_SUGGEST,
    ];
}
