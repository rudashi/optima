<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Rudashi\Optima\Services\DTO;

class Customer extends DTO
{
    public int $id;
    public string $code;
    public string $company;
    public string|null $name_line_two;
    public string|null $name_line_three;
    public ?string $name;
    public string|null $country;
    public string|null $city;
    public string|null $postal_code;
    public string|null $street;
    public string|null $building_number;
    public string|null $suite_number;
    public string|null $nip;
    public bool $deleted;

    public function __construct($args)
    {
        $this->append('name', fn () => $this->parseName($args));
        $this->cast('name_line_two', static fn ($v) => $v ?: null);
        $this->cast('name_line_three', static fn ($v) => $v ?: null);
        $this->cast('country', static fn ($v) => $v ? trim($v) : null);
        $this->cast('city', static fn ($v) => $v ? ucfirst(mb_strtolower($v)) : null);
        $this->cast('postal_code', static fn ($v) => $v ? trim($v) : null);
        $this->cast('street', fn ($v) => $v ? $this->parseStreet($v) : null);
        $this->cast('building_number', static fn ($v) => $v ? trim($v) : null);
        $this->cast('suite_number', static fn ($v) => $v ? trim($v) : null);
        $this->cast('nip', static fn ($v) => $v ?: null);

        parent::__construct($args);
    }

    /**
     * @param  array<string, mixed>|object  $args
     */
    private function parseName(array|object $args): string
    {
        return trim(implode(' ', [
            DTO::get('company', $args),
            DTO::get('name_line_two', $args),
            DTO::get('name_line_three', $args),
        ]));
    }

    private function parseStreet(string $street): string
    {
        return str_starts_with($street, 'ul')
            ? $street
            : mb_convert_case($street, MB_CASE_TITLE, 'UTF-8');
    }
}
