-- Minimal test schema for optima integration tests.
-- Contains only the columns referenced in repository source code.
-- Idempotent: safe to run multiple times (drops and recreates all objects).
-- Run with: sqlcmd -S localhost -U sa -P "..." -C -i structure.sql,data.sql

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
