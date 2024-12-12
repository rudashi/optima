<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Illuminate\Contracts\Support\Arrayable;
use Rudashi\Optima\Services\Entity\Parser;

/**
 * @implements \Illuminate\Contracts\Support\Arrayable<string, int|string|null>
 */
class Department implements Arrayable
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $user_code,
        public readonly ?int $parent_id = null,
    ) {
    }

    public static function make(object $data): self
    {
        return new self(
            id: (int) $data->id,
            name: $data->name,
            user_code: $data->user_code,
            parent_id: Parser::for($data, 'parent_id')->int(),
        );
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
