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
    public bool $deleted;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->name = trim(implode(' ', [$this->company, $this->name_line_two, $this->name_line_three]));
        $this->name_line_two = $this->name_line_two ?: null;
        $this->name_line_three = $this->name_line_three ?: null;
        $this->country = trim($this->country) ?: null;
        $this->city = $this->city
            ? ucfirst(mb_strtolower($this->city))
            : null;
        $this->postal_code = trim($this->postal_code) ?: null;
        $this->street = $this->street
            ? $this->parseStreet($this->street)
            : null;
        $this->building_number = trim($this->building_number) ?: null;
        $this->suite_number = trim($this->suite_number) ?: null;
        $this->nip = $this->nip ?: null;
    }

    private function parseStreet(string $street): string
    {
        return str_starts_with($street, 'ul')
            ? $street
            : mb_convert_case($this->street, MB_CASE_TITLE, 'UTF-8');
    }

}
