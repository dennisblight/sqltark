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
            case 1:
                return 'Select';
            case 2:
                return 'Aggregate';
            case 3:
                return 'From';
            case 4:
                return 'Join';
            case 5:
                return 'Where';
            case 6:
                return 'GroupBy';
            case 7:
                return 'Having';
            case 8:
                return 'Window';
            case 9:
                return 'Partition';
            case 10:
                return 'Frame';
            case 11:
                return 'OrderBy';
            case 12:
                return 'Limit';
            case 13:
                return 'Offset';
            case 14:
                return 'Combine';
            case 15:
                return 'CTE';
            case 16:
                return 'Insert';
            case 17:
                return 'Update';
        }
    }

    private function __construct()
    {
    }
}
