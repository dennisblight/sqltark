<?php

declare(strict_types=1);

final class MethodType
{
    public const Select = 0;
    public const Aggregate = 1;
    public const Insert = 2;
    public const Update = 3;
    public const Delete = 4;

    public static function nameOf(int $method): ?string
    {
        switch ($method) {
            case self::Select:
                return 'Select';
            case self::Aggregate:
                return 'Aggregate';
            case self::Insert:
                return 'Insert';
            case self::Update:
                return 'Update';
            case self::Delete:
                return 'Delete';
        }
    }

    private function __construct()
    {
    }
}
