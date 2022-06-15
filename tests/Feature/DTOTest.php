<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Feature\DTOTest;

use Rudashi\Optima\Tests\HelperClasses\CustomPrimaryDTO;
use Rudashi\Optima\Tests\HelperClasses\FakeDTO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('can get model key', function() {

    $dto = new FakeDTO(id: 3, order_id: 10, name: 'fake', description: 'desc');

    expect($dto->getKeyName())->toBe('id')
        ->and($dto->getKey())->toBe(3);

});

it('can get custom model key', function() {

    $dto = new CustomPrimaryDTO(id: 3, order_id: 10, name: 'fake', description: 'desc');

    expect($dto->getKeyName())->toBe('order_id')
        ->and($dto->getKey())->toBe(10);

});
