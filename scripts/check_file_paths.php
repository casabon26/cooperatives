<?php
$path = $argv[1] ?? '';
$paths = [
    'storage_app_public' => __DIR__ . '/../storage/app/public/' . ltrim($path, '/'),
    'public' => __DIR__ . '/../public/' . ltrim($path, '/'),
    'public_storage' => __DIR__ . '/../public/storage/' . ltrim($path, '/'),
];
$out = [];
foreach($paths as $k=>$p){
    $out[$k] = ['path'=>$p,'exists'=>file_exists($p)];
}
echo json_encode($out);
