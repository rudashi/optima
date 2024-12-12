<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Rudashi\Optima\Services\Entity\Parser;

class Customer
{
    public function __construct(
        public readonly int $id,
        public readonly string $code,
        public readonly string $company,
        public readonly string|null $name_line_two,
        public readonly string|null $name_line_three,
        public readonly string|null $name,
        public readonly string|null $country,
        public readonly string|null $city,
        public readonly string|null $postal_code,
        public readonly string|null $street,
        public readonly string|null $building_number,
        public readonly string|null $suite_number,
        public readonly string|null $nip,
        public readonly bool $deleted,
    ) {
    }

    public static function make(object $data): self
    {
        $name_2 = Parser::for($data, 'name_line_two')->string();
        $name_3 = Parser::for($data, 'name_line_three')->string();

        return new self(
            id: (int) $data->id,
            code: $data->code,
            company: $data->company,
            name_line_two: $name_2,
            name_line_three: $name_3,
            name: trim(implode(' ', [$data->company, $name_2, $name_3])),
            country: Parser::for($data, 'country')->trim(),
            city: Parser::for($data, 'city')->whenNotNull(fn ($value) => ucfirst(mb_strtolower($value))),
            postal_code: Parser::for($data, 'postal_code')->trim(),
            street: Parser::for($data, 'street')->whenNotNull(
                fn ($value) => str_starts_with($value, 'ul') ? $value : mb_convert_case($value, MB_CASE_TITLE, 'UTF-8')
            ),
            building_number: Parser::for($data, 'building_number')->trim(),
            suite_number: Parser::for($data, 'suite_number')->trim(),
            nip: Parser::for($data, 'nip')->string(),
            deleted: (bool) $data->deleted,
        );
    }
}
