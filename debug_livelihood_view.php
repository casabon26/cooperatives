<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = app()->make(\App\Http\Controllers\PublicController::class);
$request = new \Illuminate\Http\Request();
$view = $controller->livelihood($request);

if ($view instanceof Illuminate\View\View) {
    $data = $view->getData();
    echo "View data keys: \n";
    print_r(array_keys($data));
    echo "\nGalleries var: \n";
    if (isset($data['galleries'])) {
        echo 'count: ' . (is_countable($data['galleries']) ? count($data['galleries']) : '<non-countable>') . "\n";
        $first = is_countable($data['galleries']) && count($data['galleries']) ? $data['galleries'][0] : null;
        echo "first keys: \n";
        print_r($first ? (is_object($first) ? $first->toArray() : $first) : null);
    } else {
        echo "galleries not set\n";
    }
} else {
    var_dump($view);
}
