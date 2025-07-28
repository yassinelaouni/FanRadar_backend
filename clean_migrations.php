<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Supprimer les doublons de migrations products
    DB::table('migrations')->where('migration', '2025_07_28_135350_create_products_table')->delete();
    DB::table('migrations')->where('migration', '2025_07_28_141428_create_products_table')->delete();
    
    // Réinsérer une seule fois la bonne migration
    DB::table('migrations')->insert([
        'migration' => '2025_07_28_141428_create_products_table',
        'batch' => 6
    ]);
    
    echo "Migration products nettoyée et réinsérée\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
