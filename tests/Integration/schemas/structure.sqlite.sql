-- SQLite mirror of structure.sql, used by the `fixtures` group tests when the
-- `optima` connection falls back to sqlite (no MS_HOST set). Intentionally
-- excludes structure.sql's "smoke" table set: smoke-group tests never run
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
