<?php

namespace Account\DTO;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class TransactionReconciliationGroupData extends Collection
{
    const DATE_FORMAT = 'm/Y';

    const TYPE_DATE = 'date';
    const TYPE_TA = 'ta';

    /** @var null */
    private $ta = null;

    /** @var string */
    public $type = 'date';

    /** @var null|Carbon */
    private $date = null;

    /** @var null|string */
    public $referenceString = null;

    /**
     * @param string $ta
     */
    public function setTa(string $ta): void
    {
        $this->ta = $ta;
        $this->type = self::TYPE_TA;
        $this->setReferenceString();
    }

    /**
     * @param Carbon $date
     */
    public function setDate(Carbon $date): void
    {
        $this->date = $date;
        $this->type = self::TYPE_DATE;
        $this->setReferenceString();
    }

    private function setReferenceString(): void
    {
        if ($this->type === self::TYPE_DATE) {
            $this->referenceString = $this->date->format(self::DATE_FORMAT);
        }
        if ($this->type === self::TYPE_TA) {
            $this->referenceString = $this->ta;
        }
    }

    /**
     * @return string|null
     */
    public function getReferenceString(): ?string
    {
        return $this->referenceString;
    }
}
