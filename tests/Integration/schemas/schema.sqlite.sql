-- SQLite mirror of the "core" fixture block in schema.sql, used by the `fixtures`
-- group tests when the `optima` connection falls back to sqlite (no MS_HOST set).
-- Intentionally excludes schema.sql's "smoke" block: smoke-group tests never run
-- against sqlite (they self-skip via skipUnlessMssql()), so it isn't needed here.
--
-- Executed via DB::connection('optima')->unprepared(file_get_contents(...)).
-- No idempotency guards needed: TestCase boots a brand-new :memory: connection
-- per test (Testbench refreshes $app -> null per test in tearDown()).

ATTACH DATABASE ':memory:' AS CDN;

CREATE TABLE CDN.Kontrahenci (
    Knt_KntId       INTEGER NOT NULL PRIMARY KEY,
    Knt_Kod         TEXT    NOT NULL,
    Knt_Nazwa1      TEXT    NOT NULL,
    Knt_Nazwa2      TEXT,
    Knt_Nazwa3      TEXT,
    Knt_Kraj        TEXT,
    Knt_Miasto      TEXT,
    Knt_KodPocztowy TEXT,
    Knt_Ulica       TEXT,
    Knt_NrDomu      TEXT,
    Knt_NrLokalu    TEXT,
    Knt_Nip         TEXT,
    Knt_Nieaktywny  INTEGER NOT NULL DEFAULT 0,
    Knt_Grupa       TEXT
);

CREATE TABLE CDN.Centra (
    CNT_CntId      INTEGER NOT NULL PRIMARY KEY,
    CNT_Nazwa      TEXT    NOT NULL DEFAULT '',
    CNT_Kod        TEXT    NOT NULL DEFAULT '',
    CNT_ParentId   INTEGER,
    CNT_Nieaktywny INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE CDN.CentraKierownicy (
    CNK_CntId  INTEGER NOT NULL,
    CNK_PraId  INTEGER NOT NULL,
    CNK_Rodzaj INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE CDN.Pracidx (
    PRI_PraId      INTEGER NOT NULL PRIMARY KEY,
    PRI_Kod        TEXT    NOT NULL,
    PRI_Imie1      TEXT    NOT NULL DEFAULT '',
    PRI_Nazwisko   TEXT    NOT NULL DEFAULT '',
    PRI_Typ        INTEGER NOT NULL DEFAULT 1,
    PRI_Archiwalny INTEGER NOT NULL DEFAULT 0,
    PRI_CntId      INTEGER
);

CREATE TABLE CDN.PracEtaty (
    PRE_PreId              INTEGER NOT NULL PRIMARY KEY,
    PRE_PraId              INTEGER NOT NULL,
    PRE_HDKEmail           TEXT,
    PRE_ETADkmIdStanowisko INTEGER
);

CREATE TABLE CDN.DaneKadMod (
    DKM_DkmId INTEGER NOT NULL PRIMARY KEY,
    DKM_Nazwa TEXT
);

CREATE TABLE CDN.PracKartyRcp (
    PKR_PrcId   INTEGER NOT NULL,
    PKR_Numer   TEXT,
    PKR_OkresDo TEXT
);

INSERT INTO CDN.Kontrahenci
    (Knt_KntId, Knt_Kod, Knt_Nazwa1, Knt_Nazwa2, Knt_Nazwa3,
     Knt_Kraj, Knt_Miasto, Knt_KodPocztowy, Knt_Ulica,
     Knt_NrDomu, Knt_NrLokalu, Knt_Nip, Knt_Nieaktywny, Knt_Grupa)
VALUES
    (1, 'TEST-A',    'Test Company A',    NULL,         NULL,      NULL,     NULL,      NULL,      NULL,        NULL,  NULL, NULL,         0, NULL),
    (2, 'TEST-B',    'Test Company B',    NULL,         NULL,      NULL,     NULL,      NULL,      NULL,        NULL,  NULL, NULL,         0, NULL),
    (3, 'INACTIVE',  'Inactive Company',  NULL,         NULL,      NULL,     NULL,      NULL,      NULL,        NULL,  NULL, NULL,         1, NULL),
    (4, 'TEST-FULL', 'Test Company Full', 'Sp. z o.o.', 'Oddział', 'Polska', 'Gdańsk',  '82-500',  'ul. Polna', '26',  '1',  '5860001234', 0, 'PODWYKONAWCA');

INSERT INTO CDN.Centra
    (CNT_CntId, CNT_Nazwa, CNT_Kod, CNT_ParentId, CNT_Nieaktywny)
VALUES
    (1, 'TOTEM',     'ROOT',  NULL, 0),
    (2, 'WYDZIAŁ A', 'WYDA',  1,    0),
    (3, 'WYDZIAŁ B', 'WYDB',  1,    0),
    (4, '',          'EMPTY', 1,    0),
    (5, 'WYDZIAŁ C', 'WYDC',  1,    0);

INSERT INTO CDN.Pracidx
    (PRI_PraId, PRI_Kod, PRI_Imie1, PRI_Nazwisko, PRI_Typ, PRI_Archiwalny, PRI_CntId)
VALUES
    (1, '001E', 'Jan',   'Kowalski',   1, 0, 2),
    (2, '002E', 'Anna',  'Nowak',      1, 0, 2),
    (3, '003E', 'Piotr', 'Wiśniewski', 1, 1, 3),
    (5, '004O', 'Owner', 'Test',       2, 0, 5);

INSERT INTO CDN.CentraKierownicy
    (CNK_CntId, CNK_PraId, CNK_Rodzaj)
VALUES
    (2, 1, 0),
    (3, 2, 0),
    (4, 1, 0),
    (5, 5, 0);

INSERT INTO CDN.PracEtaty
    (PRE_PreId, PRE_PraId, PRE_HDKEmail, PRE_ETADkmIdStanowisko)
VALUES
    (1, 1, 'jan.kowalski@example.com',     1),
    (4, 1, 'jan.kowalski.new@example.com', 2),
    (2, 2, 'anna.nowak@example.com',       1),
    (3, 3, 'piotr.wisniewski@example.com', NULL),
    (5, 5, 'owner.test@example.com',       NULL);

INSERT INTO CDN.DaneKadMod
    (DKM_DkmId, DKM_Nazwa)
VALUES
    (1, 'Specjalista'),
    (2, 'Kierownik');

INSERT INTO CDN.PracKartyRcp
    (PKR_PrcId, PKR_Numer, PKR_OkresDo)
VALUES
    (1, 'RCP-001', '2999-12-31'),
    (2, 'RCP-EXP', '2000-01-01');
