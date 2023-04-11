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
    public string|null $email_warehouse;
    public string|null $shipping_notes;
    public bool $deleted;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->name = trim(implode(' ', [$this->company, $this->name_line_two, $this->name_line_three]));
        $this->name_line_two = $this->name_line_two ?: null;
        $this->name_line_three = $this->name_line_three ?: null;
        $this->country = $this->trimPropertyOrNull($this->country);
        $this->city = $this->city ? ucfirst(mb_strtolower($this->city)) : null;
        $this->postal_code = $this->trimPropertyOrNull($this->postal_code);
        $this->street = $this->street ? $this->parseStreet($this->street) : null;
        $this->building_number = $this->trimPropertyOrNull($this->building_number);
        $this->suite_number = $this->trimPropertyOrNull($this->suite_number);
        $this->nip = $this->nip ?: null;
    }

    private function parseStreet(string $street): string
    {
        return str_starts_with($street, 'ul')
            ? $street
            : mb_convert_case($this->street, MB_CASE_TITLE, 'UTF-8');
    }

    private function trimPropertyOrNull(string|null $value): string|null
    {
        return $value ? trim($value) : null;
    }
}
