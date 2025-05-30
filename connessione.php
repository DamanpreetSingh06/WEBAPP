<?php
// Connessione al database con soppressione errori
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Abilita la gestione delle eccezioni

try {
    // Connessione al database
    $conn = @mysqli_connect("localhost", "root", "", "webapp");
    
} catch (mysqli_sql_exception $e) {
    // Messaggio di errore generico per l'utente
    die("Errore di connessione. Riprova più tardi.");
    // Terminare lo script
    exit;
}
?>
