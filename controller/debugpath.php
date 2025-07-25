<?php
function debugpath() {
 echo "=== DEBUG REQUIRE ===<br>";
$debug_path = dirname(__DIR__) . '/controller/dbconnex.php';
echo "Chemin : " . $debug_path . "<br>";
echo "Fichier existe : " . (file_exists($debug_path) ? 'OUI' : 'NON') . "<br>";
echo "Fichier lisible : " . (is_readable($debug_path) ? 'OUI' : 'NON') . "<br>";

// Essayer d'inclure avec gestion d'erreur
echo "Tentative d'inclusion...<br>";
try {
    ob_start(); // Capturer les erreurs
    $result = require_once $debug_path;
    $output = ob_get_clean();
    
    echo "✅ Inclusion réussie<br>";
    if (!empty($output)) {
        echo "Sortie du fichier : " . htmlspecialchars($output) . "<br>";
    }
    
    // Vérifier si la fonction existe
    if (function_exists('dbconnex')) {
        echo "✅ Fonction dbconnex() disponible<br>";
    } else {
        echo "❌ Fonction dbconnex() NON disponible<br>";
    }
    
} catch (ParseError $e) {
    echo "❌ Erreur de syntaxe dans dbconnex.php : " . $e->getMessage() . "<br>";
} catch (Error $e) {
    echo "❌ Erreur fatale : " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "❌ Exception : " . $e->getMessage() . "<br>";
}

echo "=== FIN DEBUG ===<br><br>";
}

?>