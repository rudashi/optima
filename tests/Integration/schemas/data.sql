-- Smoke-group seed data for ptima integration tests (real MSSQL).
-- Run after structure.sql: sqlcmd -S localhost -U sa -P "..." -C -i structure.sql,data.sql

USE optima_test;
GO

-- Only smoke-group data lives here — the `fixtures`-group tests (SeededDataTest)
-- run exclusively against structure.sqlite.sql/data.sqlite.sql and never seed
-- against this MSSQL schema. Centra id=1 is shared infrastructure: every
-- department hangs off it via CNT_ParentId, and EmployeeRepository derives
-- `company` from the one Centra row with CNT_ParentId IS NULL. Ids 2-5 (with
-- their CentraKierownicy/Pracidx rows) back QueryBuilderTest's relation
-- assertions; ids 6-9 back EmployeeRepositoryTest and DepartmentRepositoryTest.
INSERT INTO [CDN].[Kontrahenci]
    ([Knt_KntId], [Knt_Kod], [Knt_Nazwa1], [Knt_Grupa], [Knt_Nieaktywny])
VALUES
    (50,    N'ANTALIS',     N'Antalis Poland', N'DOSTAWCA', 0),
    (4328,  N'TEST1',       N'Test One',       NULL,       0),
    (5160,  N'TOTEM ZOO',   N'Totem Zoo',      NULL,       0),
    (26820, N'TOTEM TEST!', N'Totem Test',     NULL,       0);
GO

INSERT INTO [CDN].[Centra]
    ([CNT_CntId], [CNT_Nazwa], [CNT_Kod], [CNT_ParentId], [CNT_Nieaktywny])
VALUES
    (1, N'TOTEM',           N'ROOT',            NULL, 0),
    (2, N'WYDZIAŁ A',       N'WYDA',            1,    0),
    (3, N'WYDZIAŁ B',       N'WYDB',            1,    0),
    (4, N'',                N'EMPTY',           1,    0),
    (5, N'WYDZIAŁ C',       N'WYDC',            1,    0),
    (6, N'DRUK',            N'DRUK',            1,    0),
    (7, N'INTROLIGATORNIA', N'INTROLIGATORNIA', 1,    0),
    (8, N'PREPRESS',        N'PREPRESS',        1,    0),
    (9, N'BIURO',           N'BIURO',           1,    0);
GO

INSERT INTO [CDN].[Pracidx]
    ([PRI_PraId], [PRI_Kod], [PRI_Imie1], [PRI_Nazwisko], [PRI_Typ], [PRI_Archiwalny], [PRI_CntId])
VALUES
    (1,  N'001E',              N'Jan',   N'Kowalski',    1, 0, 2),
    (2,  N'002E',              N'Anna',  N'Nowak',       1, 0, 2),
    (3,  N'003E',              N'Piotr', N'Wiśniewski',  1, 1, 3),
    (5,  N'004O',              N'Owner', N'Test',        2, 0, 5),
    (6,  N'130E',              N'Emp',   N'OneThirty',   1, 0, 7),
    (7,  N'012E',              N'Emp',   N'Twelve',      1, 0, 8),
    (8,  N'019E',              N'Emp',   N'Nineteen',    1, 0, 9),
    (9,  N'023E',              N'Borys', N'Test',        1, 0, 6),
    (10, N'074E_DOT_1_2_1CBR', N'Emp',   N'SeventyFour', 1, 0, 6),
    (11, N'XXX',               N'Arch',  N'Ived',        1, 1, NULL);
GO

INSERT INTO [CDN].[CentraKierownicy]
    ([CNK_CntId], [CNK_PraId], [CNK_Rodzaj])
VALUES
    (2, 1, 0),
    (3, 2, 0),
    (4, 1, 0),
    (5, 5, 0),
    (6, 9, 0),
    (7, 6, 0),
    (8, 7, 0),
    (9, 8, 0);
GO

INSERT INTO [CDN].[PracEtaty]
    ([PRE_PreId], [PRE_PraId], [PRE_HDKEmail], [PRE_ETADkmIdStanowisko])
VALUES
    (6,  6,  N'emp130@example.com',   NULL),
    (7,  7,  N'emp012@example.com',   NULL),
    (8,  8,  N'emp019@example.com',   NULL),
    (9,  9,  N'borys023@example.com', NULL),
    (10, 10, N'emp074@example.com',   NULL);
GO
