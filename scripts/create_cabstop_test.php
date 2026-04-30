<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\SelectListItem;

$item = SelectListItem::create(['group'=>'cabstop','key'=>'cabstop_bayan','label'=>'CabStop Bayan','sort_order'=>0,'active'=>true]);

echo json_encode(['id'=>$item->id,'group'=>$item->group,'key'=>$item->key,'label'=>$item->label]) . PHP_EOL;
