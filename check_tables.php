<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tables existantes:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
