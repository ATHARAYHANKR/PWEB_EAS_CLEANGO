<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

$path = 'katalog/45mje3p5Kx17vxXDQzoEqtJHFpPgt0qgQwI1Tkkn.webp';
echo 'Storage::url=' . Storage::url($path) . "\n";
echo 'exists=' . (Storage::disk('public')->exists($path) ? 'yes' : 'no') . "\n";
echo 'public file=' . (file_exists(__DIR__.'/public/storage/'.$path) ? 'yes' : 'no') . "\n";
