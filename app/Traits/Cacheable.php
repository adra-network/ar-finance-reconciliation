<?php

namespace App\Traits;

trait Cacheable
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    protected function cache(string $key, $value)
    {
        if (! isset($this->cache[$key])) {
            if (is_callable($value)) {
                $value = $value();
            }

            $this->cache[$key] = $value;
        }

        return $this->cache[$key];
    }
}
