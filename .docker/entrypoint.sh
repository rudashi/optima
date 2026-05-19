#!/bin/bash
set -e

if [ -n "$OPTIMA_DB_HOST" ]; then
    /opt/mssql-tools18/bin/sqlcmd \
        -S "${OPTIMA_DB_HOST}" \
        -U "${OPTIMA_DB_USERNAME:-sa}" \
        -P "${OPTIMA_DB_PASSWORD}" \
        -C -i tests/Integration/fixtures/schema.sql
fi

exec "$@"
