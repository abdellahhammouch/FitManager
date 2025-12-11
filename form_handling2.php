<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom_equipements = $_POST["nom_equipements"];
    $type_equipements = $_POST["type_equipements"];
    $quantity_equipements = $_POST["quantity_equipements"];
    $etat_equipements = $_POST["etat_equipements"];

    $result = $connect->query("
        INSERT INTO equipements (nom_equipements, type_equipements, quantity_equipements, etat_equipements)
        VALUES ('$nom_equipements', '$type_equipements', $quantity_equipements, '$etat_equipements')
    ");

    if (!$result) {
        echo "SQL Error: " . $connect->error;
        exit;
    }

    header("Location: index.php");
    exit;
}
?>