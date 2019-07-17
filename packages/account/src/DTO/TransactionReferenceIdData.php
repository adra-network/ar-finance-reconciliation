<?php

namespace Account\DTO;

use Carbon\Carbon;

class TransactionReferenceIdData
{
    const REFERENCE_DATE    = 'ref-date';
    const REFERENCE_TA      = 'ref-ta';
    const REFERENCE_REVERSE = 'ref-reverse';

    /** @var string */
    public $referenceOriginal = null;

    /** @var string|null */
    public $reference = null;

    /** @var Carbon|null */
    public $date = null;

    /** @var string|null */
    private $type = null;

    /**
     * TransactionReferenceIdData constructor.
     * @param string $reference
     */
    public function __construct(string $reference)
    {
        $this->referenceOriginal = $reference;

        if (preg_match('/(TA[0-9]+)/', $this->referenceOriginal, $matches)) {
            $this->reference = $matches[0];
            $this->type      = self::REFERENCE_TA;
        }
        if (preg_match('/(\<reverse\>)|(\<reversal\>)/i', $this->referenceOriginal, $matches)) {
            $this->reference = 'reverse';
            $this->type      = self::REFERENCE_REVERSE;
        }

//        if (preg_match("/(\w\w\w\s?(?:\'\d\d)?\sCC)/", $this->referenceOriginal, $matches)) {
//
//            $date = strtoupper(trim(str_replace(['CC', "'"], '', $matches[0])));
//            $date = Carbon::parse($date);
//            $this->date = $date;
//
//            $this->type = self::REFERENCE_TA;
//        }
    }

    /**
     * @return string
     */
    public function toString(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     * @return TransactionReferenceIdData|null
     */
    public static function make(string $reference = null): ?self
    {
        if (is_null($reference)) {
            return null;
        }

        return new self($reference);
    }
}
