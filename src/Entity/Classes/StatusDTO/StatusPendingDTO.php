<?php

namespace App\Entity\Classes\StatusDTO;

use App\Entity\Classes\StatusEnum;

class StatusPendingDTO extends BaseStatusDTO
{
    public int $currentStatus = StatusEnum::PENDING;
    public ?string $statusText;

    public ?int $intParam;
    public ?\DateTimeInterface $dateParam;
    public ?string $stringParam;
}
