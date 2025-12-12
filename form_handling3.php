<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "auth_check.php";
require "connect.php";

// Créer une nouvelle association
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['creer_association'])) {
    $id_cours = intval($_POST["id_cours"]);
    $equipements = $_POST["equipements"]; // Array d'IDs d'équipements
    
    // Supprimer les anciennes associations pour ce cours
    $connect->query("DELETE FROM cours_equipements WHERE id_c = $id_cours");
    
    // Insérer les nouvelles associations
    foreach ($equipements as $id_equipement) {
        $id_equipement = intval($id_equipement);
        $connect->query("INSERT INTO cours_equipements (id_c, id_e) VALUES ($id_cours, $id_equipement)");
    }
    
    header("Location: index.php#associations");
    exit;
}

// Supprimer une association complète (tous les équipements d'un cours)
if (isset($_GET["delete_association_id"])) {
    $id_cours = intval($_GET["delete_association_id"]);
    $connect->query("DELETE FROM cours_equipements WHERE id_c = $id_cours");
    header("Location: index.php#associations");
    exit;
}
?>