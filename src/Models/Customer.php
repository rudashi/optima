<?php

declare(strict_types=1);

namespace Rudashi\Optima\Models;

use Spatie\DataTransferObject\DataTransferObject;

class Customer extends DataTransferObject
{
    public int $id;
    public string $code;
    public string $company;
    public string $name_line_two;
    public string $name_line_three;
    public ?string $name = null;
    public string $country;
    public string $city;
    public string $postal_code;
    public string $street;
    public ?string $building_number = null;
    public ?string $suite_number = null;
    public string $nip;
    public int $deleted;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->name = trim(implode(' ', [$this->company, $this->name_line_two, $this->name_line_three]));
        $this->building_number = trim($this->building_number) ?: null;
        $this->suite_number = trim($this->suite_number) ?: null;
    }

}
