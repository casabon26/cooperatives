<?php
// Import CSV into cabstop_stores (or store_locations) table.
// Usage: php scripts/import_cabstop_csv.php path/to/file.csv [--place="CABSTOP MAIN"] [--no-update]

if (php_sapi_name() !== 'cli') {
    echo "Run this script from the command line\n";
    exit(1);
}

$argv0 = $argv[0] ?? 'import_cabstop_csv.php';
if ($argc < 2) {
    echo "Usage: php {$argv0} path/to/file.csv [--place=\"CABSTOP MAIN\"] [--no-update]\n";
    exit(1);
}

$file = $argv[1];
$place = 'CABSTOP MAIN';
$updateExisting = true;
foreach (array_slice($argv, 2) as $opt) {
    if (strpos($opt, '--place=') === 0) {
        $place = substr($opt, 8);
    }
    if ($opt === '--no-update') {
        $updateExisting = false;
    }
}

if (!file_exists($file)) {
    echo "File not found: {$file}\n";
    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Importing CSV: {$file}\n";
echo "Place: {$place}\n";
echo "Update existing: " . ($updateExisting ? 'yes' : 'no') . "\n";

$handle = fopen($file, 'r');
if (!$handle) {
    echo "Unable to open file.\n";
    exit(1);
}

$header = fgetcsv($handle);
if ($header === false) {
    echo "Empty CSV or invalid format.\n";
    exit(1);
}
// strip BOM from first header cell
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

// Ensure strings are UTF-8 before inserting into DB
function ensure_utf8_string($s)
{
    if ($s === null) return $s;
    // remove BOM if present
    $s = preg_replace('/^\xEF\xBB\xBF/', '', $s);
    // already UTF-8?
    if (mb_check_encoding($s, 'UTF-8')) return $s;
    // try to detect encoding and convert
    $enc = mb_detect_encoding($s, ['UTF-8', 'Windows-1252', 'ISO-8859-1', 'ASCII'], true);
    if ($enc && $enc !== 'UTF-8') {
        return mb_convert_encoding($s, 'UTF-8', $enc);
    }
    // fallback: assume Windows-1252 (common for Excel on Windows)
    return mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
}

$map = []; // csv index => field (name|owner_name|status)
foreach ($header as $i => $h) {
    $h = ensure_utf8_string($h);
    $lower = strtolower(trim($h));
    $norm = preg_replace('/[^a-z0-9]+/', '_', $lower);
    if (strpos($lower, 'business') !== false || (strpos($lower, 'store') !== false && strpos($lower, 'owner') === false)) {
        $map[$i] = 'name';
    } elseif (strpos($lower, 'owner') !== false || strpos($norm, 'name_of_owner') !== false) {
        $map[$i] = 'owner_name';
    } elseif (strpos($lower, 'status') !== false) {
        $map[$i] = 'status';
    } else {
        if (strpos($norm, 'owner') !== false) $map[$i] = 'owner_name';
        elseif (strpos($norm, 'name') !== false) $map[$i] = 'name';
    }
}

$created = $updated = $skipped = 0;
$rowNum = 0;

\Illuminate\Support\Facades\DB::beginTransaction();
try {
    while (($row = fgetcsv($handle)) !== false) {
        $rowNum++;
        $r = [];
        foreach ($map as $i => $field) {
            $val = isset($row[$i]) ? trim($row[$i]) : '';
            $r[$field] = ensure_utf8_string($val);
        }

        $name = $r['name'] ?? '';
        $owner = $r['owner_name'] ?? '';
        $statusRaw = $r['status'] ?? '';

        if ($name === '') { $skipped++; continue; }

        $s = strtolower(trim($statusRaw));
        $normStatus = null;
        if ($s === '') { $normStatus = null; }
        elseif (preg_match('/seasonal|red/', $s)) { $normStatus = 'seasonal'; }
        elseif (preg_match('/regular|green/', $s)) { $normStatus = 'regular'; }
        elseif (preg_match('/ongoing|blue|on[-_ ]?going/', $s)) { $normStatus = 'ongoing'; }
        else { $normStatus = $s; }

        $placeVal = $place;

        $existing = \App\Models\StoreLocation::where('name', $name)->where('place', $placeVal)->first();
        if ($existing) {
            if ($updateExisting) {
                $existing->owner_name = $owner ?: $existing->owner_name;
                if ($normStatus) $existing->status = $normStatus;
                $existing->save();
                $updated++;
                echo "Updated: {$name}\n";
            } else {
                $skipped++;
                echo "Skipped (exists): {$name}\n";
            }
        } else {
            \App\Models\StoreLocation::create([
                'name' => $name,
                'owner_name' => $owner ?: null,
                'status' => $normStatus ?: null,
                'place' => $placeVal,
            ]);
            $created++;
            echo "Created: {$name}\n";
        }
    }
    \Illuminate\Support\Facades\DB::commit();
} catch (\Throwable $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "Import failed: " . $e->getMessage() . "\n";
    exit(1);
}

fclose($handle);

echo "\nImport complete. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}\n";
exit(0);
