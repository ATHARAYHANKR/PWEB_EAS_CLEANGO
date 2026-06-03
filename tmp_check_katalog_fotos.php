<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Katalog;
use Illuminate\Support\Facades\Storage;

$items = Katalog::select('id_katalog','foto')->get();
foreach ($items as $k) {
    $path = $k->foto;
    if ($path) {
        echo $k->id_katalog . ' => ' . $path . "\n";
        echo '  Storage::url=' . Storage::url($path) . "\n";
        echo '  disk public exists=' . (Storage::disk('public')->exists($path) ? 'yes' : 'no') . "\n";
        echo '  public/storage exists=' . (file_exists(__DIR__ . '/public/storage/' . $path) ? 'yes' : 'no') . "\n";
        echo '  storage/app/public exists=' . (file_exists(__DIR__ . '/storage/app/public/' . $path) ? 'yes' : 'no') . "\n";
    }
}
