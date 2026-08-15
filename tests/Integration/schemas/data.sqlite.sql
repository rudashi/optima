-- Fixtures-group seed data (sqlite mirror of data.sql's rows). Loaded after
-- structure.sqlite.sql on the same connection (see tests/Pest.php) — the ATTACHed
-- CDN database persists for both calls since it's the same PDO connection.

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
