<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require "auth_check.php";
require "connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['creer_association'])) {
    $id_cours = intval($_POST["id_cours"]);
    $equipements = $_POST["equipements"];
    
    $connect->query("delete from cours_equipements where id_c = $id_cours");
    
    foreach ($equipements as $id_equipement) {
        $id_equipement = intval($id_equipement);
        $connect->query("insert into cours_equipements (id_c, id_e) values ($id_cours, $id_equipement)");
    }
    
    header("Location: index.php#associations");
    exit;
}

if (isset($_GET["delete_association_id"])) {
    $id_cours = intval($_GET["delete_association_id"]);
    $connect->query("delete from cours_equipements WHERE id_c = $id_cours");
    header("Location: index.php#associations");
    exit;
}
?>