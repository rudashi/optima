<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Enums\PolandProvinceTest;

use Rudashi\Optima\Enums\PolandProvince;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

mutates(PolandProvince::class);

it('has correct backed values for all provinces', function (PolandProvince $case, string $value) {
    expect($case->value)->toBe($value);
})->with([
    [PolandProvince::LOWER_SILESIA, 'Dolnośląskie'],
    [PolandProvince::KUYAVIAN_POMERANIAN, 'Kujawsko-Pomorskie'],
    [PolandProvince::LUBLIN, 'Lubelskie'],
    [PolandProvince::LUBUSZ, 'Lubuskie'],
    [PolandProvince::LODZ, 'Łódzkie'],
    [PolandProvince::LESSER_POLAND, 'Małopolskie'],
    [PolandProvince::MASOVIAN, 'Mazowieckie'],
    [PolandProvince::OPOLSKIE, 'Opolskie'],
    [PolandProvince::SUBCARPATHIAN, 'Podkarpackie'],
    [PolandProvince::PODLASKIE, 'Podlaskie'],
    [PolandProvince::POMERANIAN, 'Pomorskie'],
    [PolandProvince::SILESIA, 'Śląskie'],
    [PolandProvince::HOLY_CROSS, 'Świętokrzyskie'],
    [PolandProvince::WARMIA_MASURIA, 'Warmińsko-Mazurskie'],
    [PolandProvince::GREATER_POLAND, 'Wielkopolskie'],
    [PolandProvince::WEST_POMERANIAN, 'Zachodniopomorskie'],
]);

it('has exactly sixteen cases matching all Polish provinces', function () {
    expect(PolandProvince::cases())->toHaveCount(16);
});
