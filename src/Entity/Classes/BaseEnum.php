<?php

namespace App\Entity\Classes;

abstract class BaseEnum
{
    private static $constCacheArray = NULL;

    abstract public static function getDefault(): int;

    abstract public static function getDefaultName(): string;

    public static function getNameById(int $statusId): string
    {
        $names = array_flip(self::getConstants());

        if (!isset($names[$statusId])) {
            throw new \Exception("Invalid status value", 1);
        }

        return $names[$statusId];
    }

    public static function getIdByName(string $statusName): int
    {
        $names = self::getConstants();

        if (!isset($names[$statusName])) {
            throw new \Exception("Invalid status value", 1);
        }

        return $names[$statusName];
    }

    public static function getConstants()
    {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false)
    {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value, $strict = true)
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }

}
