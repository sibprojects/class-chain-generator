<?php

namespace App\Entity\Classes\StatusDTO;

use App\Entity\Classes\StatusEnum;

class StatusCompletedDTO extends BaseStatusDTO
{
    public int $currentStatus = StatusEnum::COMPLETED;
    public ?string $statusText;

    public ?int $intParam;
    public ?\DateTimeInterface $dateParam;
    public ?string $stringParam;
}
