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
    $pos = strpos($html, 'gallery-thumb');
    if ($pos === false) {
        echo "gallery-thumb not found\n";
    } else {
        $start = max(0, $pos - 600);
        $snippet = substr($html, $start, 1400);
        echo $snippet;
    }
} else {
    var_dump($view);
}
