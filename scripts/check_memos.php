<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Memorandum;

$items = Memorandum::all();
foreach($items as $m){
    $pub = $m->published_at;
    $created = $m->created_at;
    echo $m->id . " | published_at: ";
    if($pub instanceof DateTimeInterface){
        echo $pub->format(DATE_ATOM);
    } elseif($pub){
        echo $pub;
    } else {
        echo 'NULL';
    }
    echo " | created_at: ";
    if($created instanceof DateTimeInterface){
        echo $created->format(DATE_ATOM);
    } elseif($created){
        echo $created;
    } else {
        echo 'NULL';
    }
    echo PHP_EOL;
}
