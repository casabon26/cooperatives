<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StoreLocation;

$place = $argv[1] ?? 'cabstop_bayan';
$store_type = $argv[2] ?? '';

$query = StoreLocation::query();
if($place) $query->where('place', $place);
if($store_type) $query->where('store_type', $store_type);
$rows = $query->get();
foreach($rows as $r) echo json_encode(['id'=>$r->id,'name'=>$r->name,'place'=>$r->place,'store_type'=>$r->store_type]) . PHP_EOL;
