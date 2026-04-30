<?php
// List enterprise store locations (lat & lng present)
if (php_sapi_name() !== 'cli') {
    echo "Run this script from the command line\n";
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StoreLocation;

$rows = StoreLocation::whereNotNull('lat')->whereNotNull('lng')->get();
echo "Enterprise rows remaining: " . $rows->count() . "\n";
foreach ($rows as $r) {
    echo $r->id . " - " . ($r->name ?? '(no name)') . "\n";
}

exit(0);
