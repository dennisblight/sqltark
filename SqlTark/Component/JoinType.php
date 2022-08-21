<?php

declare(strict_types=1);

namespace SqlTark\Component;

final class JoinType
{
    public const Join = 0;
    public const InnerJoin = 1;
    public const LeftJoin = 2;
    public const RightJoin = 3;
    public const OuterJoin = 4;
    public const CrossJoin = 5;
    public const NaturalJoin = 6;
    public const LeftOuterJoin = 7;
    public const RightOuterJoin = 8;
    public const FullOuterJoin = 9;

    public static function nameOf(int $type): ?string
    {
        switch ($type) {
            case self::Join:
                return 'Join';
            case self::InnerJoin:
                return 'InnerJoin';
            case self::LeftJoin:
                return 'LeftJoin';
            case self::RightJoin:
                return 'RightJoin';
            case self::OuterJoin:
                return 'OuterJoin';
            case self::NaturalJoin:
                return 'NaturalJoin';
            case self::CrossJoin:
                return 'CrossJoin';
            case self::LeftOuterJoin:
                return 'LeftOuterJoin';
            case self::RightOuterJoin:
                return 'RightOuterJoin';
            case self::FullOuterJoin:
                return 'FullOuterJoin';
        }
    }

    private function __construct()
    {
    }
}
