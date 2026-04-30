<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app()->make(\App\Http\Controllers\PublicController::class);
$request = new \Illuminate\Http\Request();
$view = $controller->livelihood($request);

if ($view instanceof Illuminate\View\View) {
    $html = $view->render();
    $hasGallery = strpos($html, 'gallery-thumb') !== false;
    $hasGalleryHeader = strpos($html, '<h5 class="card-title mb-2">Gallery</h5>') !== false;
    echo json_encode(['hasGalleryThumb' => $hasGallery, 'hasGalleryHeader' => $hasGalleryHeader]);
} else {
    var_dump($view);
}
