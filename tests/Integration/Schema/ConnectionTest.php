<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Schema\ConnectionTest;

use Illuminate\Support\Facades\DB;
use PDO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

it('resolves optima connection', function () {
    expect(DB::connection('optima')->getPdo())
        ->toBeInstanceOf(PDO::class);
});

it('Kontrahenci table has expected columns', function () {
    $columns = DB::connection('optima')->select(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = 'CDN' AND TABLE_NAME = 'Kontrahenci'"
    );

    $names = array_map(fn ($col) => $col->COLUMN_NAME, $columns);

    expect($names)->toContain(
        'Knt_KntId',
        'Knt_Kod',
        'Knt_Nazwa1',
        'Knt_Nieaktywny',
        'Knt_Nip',
    );
});

it('Centra table has expected columns', function () {
    $columns = DB::connection('optima')->select(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = 'CDN' AND TABLE_NAME = 'Centra'"
    );

    $names = array_map(fn ($col) => $col->COLUMN_NAME, $columns);

    expect($names)->toContain(
        'CNT_CntId',
        'CNT_Nazwa',
        'CNT_Kod',
        'CNT_ParentId',
        'CNT_Nieaktywny',
    );
});

it('Pracidx table has expected columns', function () {
    $columns = DB::connection('optima')->select(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = 'CDN' AND TABLE_NAME = 'Pracidx'"
    );

    $names = array_map(fn ($col) => $col->COLUMN_NAME, $columns);

    expect($names)->toContain(
        'PRI_PraId',
        'PRI_Kod',
        'PRI_Imie1',
        'PRI_Nazwisko',
        'PRI_Typ',
        'PRI_Archiwalny',
    );
});

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
