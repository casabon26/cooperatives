<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app()->make(\App\Http\Controllers\PublicController::class);
$view = $controller->livelihood(new \Illuminate\Http\Request());

if ($view instanceof Illuminate\View\View) {
    $html = $view->render();
    $pos = strpos($html, '/galleries/');
    if ($pos === false) {
        echo "no /galleries/ found\n";
    } else {
        $start = max(0, $pos - 400);
        $snippet = substr($html, $start, 1200);
        echo $snippet;
    }
} else {
    var_dump($view);
}
