<?php

declare(strict_types=1);

expect()->extend('toBeNullableString', function () {
    return $this->value === null ? $this : $this->toBeString();
});

expect()->extend('toBeNullableInt', function () {
    return $this->value === null ? $this : $this->toBeInt();
});
