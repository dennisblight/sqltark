<?php

declare(strict_types=1);

namespace SqlTark\Component;

final class ComponentType
{
    public const Select = 1;
    public const Aggregate = 2;
    public const From = 3;
    public const Join = 4;
    public const Where = 5;
    public const GroupBy = 6;
    public const Having = 7;
    public const Window = 8;
    public const Partition = 9;
    public const Frame = 10;
    public const OrderBy = 11;
    public const Limit = 12;
    public const Offset = 13;
    public const Combine = 14;
    public const CTE = 15;
    public const Insert = 16;
    public const Update = 17;

    public static function nameOf(int $component): ?string
    {
        switch ($component) {
            case self::Select:
                return 'Select';
            case self::Aggregate:
                return 'Aggregate';
            case self::From:
                return 'From';
            case self::Join:
                return 'Join';
            case self::Where:
                return 'Where';
            case self::GroupBy:
                return 'GroupBy';
            case self::Having:
                return 'Having';
            case self::Window:
                return 'Window';
            case self::Partition:
                return 'Partition';
            case self::Frame:
                return 'Frame';
            case self::OrderBy:
                return 'OrderBy';
            case self::Limit:
                return 'Limit';
            case self::Offset:
                return 'Offset';
            case self::Combine:
                return 'Combine';
            case self::CTE:
                return 'CTE';
            case self::Insert:
                return 'Insert';
            case self::Update:
                return 'Update';
        }

        return null;
    }

    private function __construct()
    {
    }
}
