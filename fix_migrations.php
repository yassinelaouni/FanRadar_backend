<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Insérer l'enregistrement de migration manquant
    DB::table('migrations')->insert([
        'migration' => '2025_07_28_141428_create_products_table',
        'batch' => 6
    ]);
    
    echo "Migration products ajoutée dans la table migrations\n";
    
    // Afficher toutes les migrations
    $migrations = DB::table('migrations')->orderBy('batch')->get();
    echo "\nMigrations enregistrées:\n";
    foreach ($migrations as $migration) {
        echo "- {$migration->migration} (batch: {$migration->batch})\n";
    }
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
