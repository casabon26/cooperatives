<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Cooperative;
$coop = Cooperative::find(16);
if (!$coop) { echo "Coop 16 not found\n"; exit(1); }
echo "Name: " . ($coop->name ?? '') . "\n";
echo "Description: " . substr($coop->description??'','0',200) . "\n";
echo "Mission: " . substr($coop->mission??'','0',200) . "\n";
echo "Vision: " . substr($coop->vision??'','0',200) . "\n";
echo "Services (preview): " . substr($coop->services??'','0',200) . "\n";
exit(0);
