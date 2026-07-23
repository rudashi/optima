-- Minimal test schema for rudashi/optima integration tests.
-- Contains only the columns referenced in repository source code.
-- Idempotent: safe to run multiple times (drops and recreates all objects).
-- Run with: sqlcmd -S localhost -U sa -P "..." -C -i schema.sql

IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'optima_test')
    CREATE DATABASE optima_test;
GO

USE optima_test;
GO

IF DB_NAME() <> 'optima_test'
    THROW 50001, 'Refusing to seed: current DB is not optima_test', 1;
GO

-- Drop tables in dependency order before recreating
DROP TABLE IF EXISTS [CDN].[PracKartyRcp];
DROP TABLE IF EXISTS [CDN].[PracEtaty];
DROP TABLE IF EXISTS [CDN].[DaneKadMod];
DROP TABLE IF EXISTS [CDN].[CentraKierownicy];
DROP TABLE IF EXISTS [CDN].[Pracidx];
DROP TABLE IF EXISTS [CDN].[Centra];
DROP TABLE IF EXISTS [CDN].[Kontrahenci];
GO

IF NOT EXISTS (SELECT * FROM sys.schemas WHERE name = 'CDN')
    EXEC('CREATE SCHEMA CDN');
GO

-- ============================================================
-- Customers
-- ============================================================
CREATE TABLE [CDN].[Kontrahenci] (
    [Knt_KntId]       INT            NOT NULL PRIMARY KEY,
    [Knt_Kod]         NVARCHAR(50)   NOT NULL,
    [Knt_Nazwa1]      NVARCHAR(255)  NOT NULL,
    [Knt_Nazwa2]      NVARCHAR(255)  NULL,
    [Knt_Nazwa3]      NVARCHAR(255)  NULL,
    [Knt_Kraj]        NVARCHAR(50)   NULL,
    [Knt_Miasto]      NVARCHAR(100)  NULL,
    [Knt_KodPocztowy] NVARCHAR(10)   NULL,
    [Knt_Ulica]       NVARCHAR(100)  NULL,
    [Knt_NrDomu]      NVARCHAR(20)   NULL,
    [Knt_NrLokalu]    NVARCHAR(20)   NULL,
    [Knt_Nip]         NVARCHAR(20)   NULL,
    [Knt_Nieaktywny]  TINYINT        NOT NULL DEFAULT 0,
    [Knt_Grupa]       NVARCHAR(50)   NULL,
);
GO

-- ============================================================
-- Departments
-- ============================================================
CREATE TABLE [CDN].[Centra] (
    [CNT_CntId]      INT           NOT NULL PRIMARY KEY,
    [CNT_Nazwa]      NVARCHAR(100) NOT NULL DEFAULT '',
    [CNT_Kod]        NVARCHAR(50)  NOT NULL DEFAULT '',
    [CNT_ParentId]   INT           NULL,
    [CNT_Nieaktywny] TINYINT       NOT NULL DEFAULT 0,
);
GO

CREATE TABLE [CDN].[CentraKierownicy] (
    [CNK_CntId]  INT     NOT NULL,
    [CNK_PraId]  INT     NOT NULL,
    [CNK_Rodzaj] TINYINT NOT NULL DEFAULT 0,
);
GO

-- ============================================================
-- Employees
-- ============================================================
CREATE TABLE [CDN].[Pracidx] (
    [PRI_PraId]      INT           NOT NULL PRIMARY KEY,
    [PRI_Kod]        NVARCHAR(50)  NOT NULL,
    [PRI_Imie1]      NVARCHAR(100) NOT NULL DEFAULT '',
    [PRI_Nazwisko]   NVARCHAR(100) NOT NULL DEFAULT '',
    [PRI_Typ]        TINYINT       NOT NULL DEFAULT 1,
    [PRI_Archiwalny] TINYINT       NOT NULL DEFAULT 0,
    [PRI_CntId]      INT           NULL,
);
GO

CREATE TABLE [CDN].[PracEtaty] (
    [PRE_PreId]                  INT           NOT NULL PRIMARY KEY,
    [PRE_PraId]                  INT           NOT NULL,
    [PRE_HDKEmail]               NVARCHAR(100) NULL,
    [PRE_ETADkmIdStanowisko]     INT           NULL,
);
GO

CREATE TABLE [CDN].[DaneKadMod] (
    [DKM_DkmId] INT           NOT NULL PRIMARY KEY,
    [DKM_Nazwa] NVARCHAR(100) NULL,
);
GO

CREATE TABLE [CDN].[PracKartyRcp] (
    [PKR_PrcId]   INT           NOT NULL,
    [PKR_Numer]   NVARCHAR(50)  NULL,
    [PKR_OkresDo] DATETIME      NULL,
);
GO

-- ============================================================
-- Fixtures — neutral test data
-- ============================================================
INSERT INTO [CDN].[Kontrahenci]
    ([Knt_KntId], [Knt_Kod], [Knt_Nazwa1], [Knt_Nazwa2], [Knt_Nazwa3],
     [Knt_Kraj], [Knt_Miasto], [Knt_KodPocztowy], [Knt_Ulica],
     [Knt_NrDomu], [Knt_NrLokalu], [Knt_Nip], [Knt_Nieaktywny], [Knt_Grupa])
VALUES
    (1, N'TEST-A',    N'Test Company A',    NULL,         NULL,       NULL,      NULL,       NULL,      NULL,         NULL,  NULL, NULL,           0, NULL),
    (2, N'TEST-B',    N'Test Company B',    NULL,         NULL,       NULL,      NULL,       NULL,      NULL,         NULL,  NULL, NULL,           0, NULL),
    (3, N'INACTIVE',  N'Inactive Company',  NULL,         NULL,       NULL,      NULL,       NULL,      NULL,         NULL,  NULL, NULL,           1, NULL),
    (4, N'TEST-FULL', N'Test Company Full', N'Sp. z o.o.', N'Oddział', N'Polska', N'GDAŃSK', N'82-500', N'ul. Polna', N'26', N'1', N'5860001234', 0, N'PODWYKONAWCA');
GO

INSERT INTO [CDN].[Centra]
    ([CNT_CntId], [CNT_Nazwa], [CNT_Kod], [CNT_ParentId], [CNT_Nieaktywny])
VALUES
    (1, N'TOTEM',     N'ROOT',  NULL, 0),
    (2, N'WYDZIAŁ A', N'WYDA',  1,    0),
    (3, N'WYDZIAŁ B', N'WYDB',  1,    0),
    (4, N'',          N'EMPTY', 1,    0),
    (5, N'WYDZIAŁ C', N'WYDC',  1,    0);
GO

INSERT INTO [CDN].[Pracidx]
    ([PRI_PraId], [PRI_Kod], [PRI_Imie1], [PRI_Nazwisko], [PRI_Typ], [PRI_Archiwalny], [PRI_CntId])
VALUES
    (1, N'001E', N'Jan',   N'Kowalski',   1, 0, 2),
    (2, N'002E', N'Anna',  N'Nowak',      1, 0, 2),
    (3, N'003E', N'Piotr', N'Wiśniewski', 1, 1, 3),
    (5, N'004O', N'Owner', N'Test',       2, 0, 5);
GO

INSERT INTO [CDN].[CentraKierownicy]
    ([CNK_CntId], [CNK_PraId], [CNK_Rodzaj])
VALUES
    (2, 1, 0),
    (3, 2, 0),
    (4, 1, 0),
    (5, 5, 0);
GO

INSERT INTO [CDN].[PracEtaty]
    ([PRE_PreId], [PRE_PraId], [PRE_HDKEmail], [PRE_ETADkmIdStanowisko])
VALUES
    (1, 1, N'jan.kowalski@example.com',     1),
    (4, 1, N'jan.kowalski.new@example.com', 2),
    (2, 2, N'anna.nowak@example.com',       1),
    (3, 3, N'piotr.wisniewski@example.com', NULL),
    (5, 5, N'owner.test@example.com',       NULL);
GO

INSERT INTO [CDN].[DaneKadMod]
    ([DKM_DkmId], [DKM_Nazwa])
VALUES
    (1, N'Specjalista'),
    (2, N'Kierownik');
GO

INSERT INTO [CDN].[PracKartyRcp]
    ([PKR_PrcId], [PKR_Numer], [PKR_OkresDo])
VALUES
    (1, N'RCP-001', '29991231'),
    (2, N'RCP-EXP', '20000101');
GO

-- ============================================================
-- Smoke fixtures
-- ============================================================
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
    (6, N'DRUK',            N'DRUK',            1, 0),
    (7, N'INTROLIGATORNIA', N'INTROLIGATORNIA', 1, 0),
    (8, N'PREPRESS',        N'PREPRESS',        1, 0),
    (9, N'BIURO',           N'BIURO',           1, 0);
GO

INSERT INTO [CDN].[Pracidx]
    ([PRI_PraId], [PRI_Kod], [PRI_Imie1], [PRI_Nazwisko], [PRI_Typ], [PRI_Archiwalny], [PRI_CntId])
VALUES
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
