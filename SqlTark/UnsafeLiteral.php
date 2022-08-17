<?php

declare(strict_types=1);

namespace SqlTark;

class UnsafeLiteral
{
    /**
     * @var string
     */
    protected $value;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
    }

    public function __construct(string $value, bool $replaceQuotes)
    {
        if (is_null($value)) {
            $value = '';
        }

        if ($replaceQuotes) {
            $value = str_replace("'", "''", $value);
        }

        $this->value = $value;
    }
}
