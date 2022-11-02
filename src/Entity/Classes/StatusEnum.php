<?php

namespace App\Entity\Classes;

class StatusEnum extends BaseEnum
{
    public const PENDING = 0;
    public const COMPLETED = 1;
    public const CANCELED = 2;

    public static function getDefault(): int
    {
        return self::PENDING;
    }

    public static function getDefaultName(): string
    {
        return parent::getNameById(self::PENDING);
    }
}
