<?php

declare(strict_types=1);

namespace Rudashi\Optima\Services;

use Spatie\DataTransferObject\DataTransferObject;

class DTO extends DataTransferObject
{

    protected string $primaryKey = 'id';

    public function getKey(): mixed
    {
        return $this->{$this->getKeyName()};
    }

    public function getKeyName(): string
    {
        return $this->primaryKey;
    }

}
