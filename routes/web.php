<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Routes are split into partials under routes/web/ for readability.
| ORDER MATTERS: Laravel matches in registration order, and some URIs
| are intentionally duplicated (first match wins), so do not reorder.
|
*/

require __DIR__.'/web/auth.php';
require __DIR__.'/web/admin.php';
require __DIR__.'/web/dispatcher.php';
require __DIR__.'/web/admin-sections.php';
require __DIR__.'/web/seo.php';
require __DIR__.'/web/admin-settings.php';
require __DIR__.'/web/village-site.php';
require __DIR__.'/web/village-admin.php';
require __DIR__.'/web/section-requests.php';
require __DIR__.'/web/public-misc.php';
require __DIR__.'/web/tariffs.php';
require __DIR__.'/web/expert.php';
require __DIR__.'/web/tariff-invoices.php';
require __DIR__.'/web/downloads.php';

require __DIR__.'/web/levy.php';
