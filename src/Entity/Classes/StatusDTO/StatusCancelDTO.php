<?php

namespace App\Entity\Classes\StatusDTO;

use App\Entity\Classes\StatusEnum;

class StatusCanceledDTO extends BaseStatusDTO
{
    public int $currentStatus = StatusEnum::CANCELED;
    public ?string $statusText;

    public ?int $intParam;
    public ?\DateTimeInterface $dateParam;
    public ?string $stringParam;
}
