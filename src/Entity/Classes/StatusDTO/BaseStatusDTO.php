<?php

namespace App\Entity\Classes\StatusDTO;

use App\Entity\Classes\StatusEnum;

class BaseStatusDTO
{
    public int $currentStatus = StatusEnum::PENDING;
    public ?string $statusText;

    public function validate()
    {
        return true;
    }

    public function apply()
    {
        if ($this->validate()) {
            return true;
        } else {
            throw new \Exception('Can\'t change status');
        }
    }

    private function getProperties()
    {
        $calledClass = get_called_class();
        $reflect = new \ReflectionClass($calledClass);
        return $reflect->getProperties();
    }
}