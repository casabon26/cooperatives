<?php
use Throwable;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \App\Models\Gallery::count();
    $first = \App\Models\Gallery::orderByDesc('created_at')->first();
    $out = [
        'count' => $count,
        'first' => $first ? $first->toArray() : null,
        'first_image_url' => $first ? $first->image_url : null,
    ];
    echo json_encode($out, JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    echo json_encode(['error' => (string)$e]);
}
