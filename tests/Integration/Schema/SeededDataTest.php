<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Schema\SeededDataTest;

use Illuminate\Support\Facades\DB;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('fixtures');

it('finds a customer by code from seeded fixture', function () {
    $result = DB::connection('optima')
        ->table('CDN.Kontrahenci')
        ->where('Knt_Kod', 'TEST-A')
        ->where('Knt_Nieaktywny', 0)
        ->first();

    expect($result)
        ->not->toBeNull()
        ->and($result->Knt_Nazwa1)->toBe('Test Company A');
});
