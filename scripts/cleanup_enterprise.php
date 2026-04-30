<?php
// Backup and delete enterprise store locations except for specified names.
// Usage: php scripts/cleanup_enterprise.php

if (php_sapi_name() !== 'cli') {
    echo "Run this script from the command line\n";
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StoreLocation;

// Names to retain (case-insensitive, trimmed)
$keep = [ 'pares', 'buko ni dan' ];

echo "Backing up enterprise store locations (lat & lng present), excluding: " . implode(', ', $keep) . "\n";

$query = StoreLocation::query()->whereNotNull('lat')->whereNotNull('lng')
    ->whereRaw('LOWER(TRIM(name)) NOT IN (' . implode(',', array_fill(0, count($keep), '?')) . ')', $keep);

$toDelete = $query->get();
$count = $toDelete->count();
if ($count === 0) {
    echo "No enterprise records found for deletion.\n";
    exit(0);
}

$ts = date('Ymd_His');
$backupDir = storage_path('imports');
if (!is_dir($backupDir)) @mkdir($backupDir, 0777, true);
$backupPath = $backupDir . DIRECTORY_SEPARATOR . "enterprise_backup_{$ts}.csv";

$fp = fopen($backupPath, 'w');
if (!$fp) {
    echo "Could not create backup file: {$backupPath}\n";
    exit(1);
}

$headers = ['id','name','owner_name','status','address','lat','lng','category','tags','map_url','place','store_type','description','icon_url','created_at','updated_at'];
fputcsv($fp, $headers);
foreach ($toDelete as $row) {
    $line = [];
    foreach ($headers as $h) {
        $line[] = isset($row->{$h}) ? $row->{$h} : '';
    }
    fputcsv($fp, $line);
}
fclose($fp);

echo "Backup saved to: {$backupPath} (rows: {$count})\n";

// Perform deletion within transaction
DB::beginTransaction();
try {
    $ids = $toDelete->pluck('id')->toArray();
    $deleted = StoreLocation::whereIn('id', $ids)->delete();
    DB::commit();
    echo "Deleted {$deleted} enterprise rows.\n";
    echo "Done.\n";
    exit(0);
} catch (Throwable $e) {
    DB::rollBack();
    echo "Deletion failed: " . $e->getMessage() . "\n";
    exit(1);
}
