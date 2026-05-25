#!/bin/bash
set -e

if [ "$OPTIMA_SEED_FIXTURES" = "1" ] && [[ "$MS_HOST" =~ ^(localhost|127\.0\.0\.1|mssql)$ ]]; then
    /opt/mssql-tools18/bin/sqlcmd \
        -S "$MS_HOST" \
        -U "${MS_USERNAME:-sa}" \
        -P "$MS_PASSWORD" \
        -C -i tests/Integration/fixtures/schema.sql
fi

exec "$@"
