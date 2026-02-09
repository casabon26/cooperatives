<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Memorandum;

$items = Memorandum::all();
foreach($items as $m){
    $path = $m->file_path;
    echo "ID: {$m->id}\n";
    echo " file_path: ".var_export($path, true)."\n";
    if(!$path){
        echo " no file_path set\n\n";
        continue;
    }
    $storagePath = storage_path('app/public/'.ltrim($path,'/'));
    $publicPath = public_path(ltrim($path,'/'));
    $publicStoragePath = public_path('storage/'.ltrim($path,'/'));
    echo " storage_path: {$storagePath}\n";
    echo " public_path: {$publicPath}\n";
    echo " public_storage_path: {$publicStoragePath}\n";
    echo " exists(storage): ".(file_exists($storagePath)?'yes':'no')."\n";
    echo " exists(public): ".(file_exists($publicPath)?'yes':'no')."\n";
    echo " exists(public_storage): ".(file_exists($publicStoragePath)?'yes':'no')."\n";
    echo "\n";
}
