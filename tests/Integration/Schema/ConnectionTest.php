<?php

declare(strict_types=1);

namespace Rudashi\Optima\Tests\Integration\Schema\ConnectionTest;

use Illuminate\Support\Facades\DB;
use PDO;
use Rudashi\Optima\Tests\TestCase;

uses(TestCase::class);

pest()->group('smoke');

it('resolves optima connection', function () {
    expect(DB::connection('optima')->getPdo())
        ->toBeInstanceOf(PDO::class);
});

it('table has expected columns', function (string $table, array $expected) {
    $columns = DB::connection('optima')->select(
        "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = 'CDN' AND TABLE_NAME = ?",
        [$table]
    );

    $names = array_map(fn ($col) => $col->COLUMN_NAME, $columns);

    expect($names)->toContain(...$expected);
})->with([
    'Kontrahenci' => ['Kontrahenci', ['Knt_KntId', 'Knt_Kod', 'Knt_Nazwa1', 'Knt_Nieaktywny', 'Knt_Nip', 'Knt_Grupa']],
    'Centra'      => ['Centra', ['CNT_CntId', 'CNT_Nazwa', 'CNT_Kod', 'CNT_ParentId', 'CNT_Nieaktywny']],
    'Pracidx'     => ['Pracidx', ['PRI_PraId', 'PRI_Kod', 'PRI_Imie1', 'PRI_Nazwisko', 'PRI_Typ', 'PRI_Archiwalny']],
]);
