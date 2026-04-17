<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Business;
use Illuminate\Support\Facades\Schema;

$columns = Schema::getColumnListing('business');
echo "COLUMNS: " . implode(', ', $columns) . "\n";

$business = Business::first();
if ($business) {
    echo "SAMPLE DATA: " . json_encode($business->toArray()) . "\n";
} else {
    echo "NO BUSINESS FOUND\n";
}
