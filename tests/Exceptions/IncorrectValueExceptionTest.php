<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Exceptions\IncorrectValueExceptionTest;

use Rudashi\Optima\Exceptions\IncorrectValueException;
use Rudashi\Optima\Tests\TestCase;
use UnexpectedValueException;

uses(TestCase::class);

mutates(IncorrectValueException::class);

it('defaults the code to 422 when none is given', function () {
    $exception = new IncorrectValueException('Invalid value');

    expect($exception->getCode())->toBe(422)
        ->and($exception->getMessage())->toBe('Invalid value');
});

it('keeps a provided non-zero code', function () {
    expect((new IncorrectValueException('Invalid value', 500))->getCode())->toBe(500);
});

it('extends UnexpectedValueException and defaults to an empty message', function () {
    $exception = new IncorrectValueException();

    expect($exception)->toBeInstanceOf(UnexpectedValueException::class)
        ->and($exception->getMessage())->toBe('')
        ->and($exception->getCode())->toBe(422);
});
