#!/usr/bin/env bash
# Run the test suite safely (SQLite in-memory, never the production database).
#
#   sudo bash scripts/test.sh            # whole suite
#   sudo bash scripts/test.sh --filter=TariffWorkflowTest
#
# tests/TestCase.php refuses to run if the environment resolves to anything
# other than sqlite::memory:, so this can never touch the live MySQL database.
set -euo pipefail

APP_DIR=/var/www/chegeniroosta
cd "$APP_DIR"

if [ "$(id -un)" = "root" ]; then
    exec sudo -u www-data /usr/bin/php artisan test "$@"
fi

exec /usr/bin/php artisan test "$@"
