#!/bin/bash
SH_DIR="$(cd "$(dirname "$0")" && pwd)"
APP_ROOT="$(dirname "$SH_DIR")"
php "$APP_ROOT/src/pg-convert.php" "$@"