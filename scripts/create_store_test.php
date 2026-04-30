<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StoreLocation;

$store = StoreLocation::create([
    'name' => 'TEST STORE ' . time(),
    'address' => '123 Test St',
    'place' => 'cabstop_bayan',
    'store_type' => 'food'
]);

echo json_encode(['id'=>$store->id, 'place'=>$store->place, 'store_type'=>$store->store_type]) . PHP_EOL;
