<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$stores = App\Models\StoreLocation::all();
foreach($stores as $s) {
    echo json_encode(['id' => $s->id, 'name' => $s->name, 'place' => $s->place, 'store_type' => $s->store_type]) . PHP_EOL;
}
