<?php

namespace Account\DTO;

class TransactionReferenceIdData
{
    const REFERENCE_DATE = 'ref-date';
    const REFERENCE_TA = 'ref-ta';
    const REFERENCE_REVERSE = 'ref-reverse';

    /** @var string */
    public $referenceOriginal = null;

    /** @var string|null */
    public $reference = null;

    /** @var string|null */
    private $type = null;

    const MONTHS = [
        'January' => 'Jan',
        'February' => 'Feb',
        'March' => 'Mar',
        'April' => 'Apr',
        'May' => 'May',
        'June' => 'Jun',
        'July' => 'Jul',
        'August' => 'Aug',
        'September' => 'Sep',
        'October' => 'Oct',
        'November' => 'Nov',
        'December' => 'Dec',
    ];

    /**
     * TransactionReferenceIdData constructor.
     * @param string $reference
     */
    public function __construct(string $reference)
    {
        $this->referenceOriginal = $reference;

        //MATCH TA REFERENCES
        if (preg_match('/(TA[0-9]+)/', $this->referenceOriginal, $matches)) {
            $this->reference = $matches[0];
            $this->type = self::REFERENCE_TA;
        }

        //MATCH REVERSE REFERENCES
        if (preg_match('/(\<reverse\>)|(\<reversal\>)/i', $this->referenceOriginal, $matches)) {
            $this->reference = 'reverse';
            $this->type = self::REFERENCE_REVERSE;
        }

        //MATCH DATE REFERENCES
        $ref = str_replace(array_keys(self::MONTHS), array_values(self::MONTHS), $this->referenceOriginal);
        $regex = sprintf("/(%s\s?(?:\'\d\d)?)\s?CC/i", implode('|', array_values(self::MONTHS)));
        if (preg_match($regex, $ref, $matches)) {
            $date = trim(
                str_replace(
                    "'",
                    ' ',
                    str_replace(
                        " '",
                        "'",
                        strtoupper(
                            $matches[1]
                        )
                    )
                )
            );

//            [$month, $year] = explode(' ', $date);

            $this->reference = $date;
            $this->type = self::REFERENCE_DATE;
        }
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
