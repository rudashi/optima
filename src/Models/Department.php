<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Illuminate\Contracts\Support\Arrayable;
use Rudashi\Optima\Services\Entity\Parser;
use stdClass;

/**
 * @implements \Illuminate\Contracts\Support\Arrayable<string, int|string|null>
 */
readonly class Department implements Arrayable
{
    public function __construct(
        public int $id,
        public string $name,
        public string $user_code,
        public ?int $parent_id = null,
    ) {
    }

    public static function make(stdClass $data): self
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
