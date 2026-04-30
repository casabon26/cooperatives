<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$g = \App\Models\Gallery::where('section','livelihood')->get()->pluck('path')->toArray();
echo json_encode($g, JSON_PRETTY_PRINT);
