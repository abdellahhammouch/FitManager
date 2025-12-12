<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "auth_check.php";
require "connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom_cours = $_POST["nom_cours"];
    $categories_cours = $_POST["categories_cours"];
    $date_cours = $_POST["date_cours"];
    $heure_cours = $_POST["heure_cours"];
    $duree_cours = $_POST["duree_cours"];
    $max_participants = $_POST["max_participants"];

    $result = $connect->query("
        insert into cours (nom_cours, categories_cours, date_cours, heure_cours, duree_cours, max_participants)
        values ('$nom_cours', '$categories_cours', '$date_cours', '$heure_cours', '$duree_cours', $max_participants)
    ");

    if (!$result) {
        echo "SQL Error: " . $connect->error;
        exit;
    }

    header("Location: index.php");
    exit;
}
?>
