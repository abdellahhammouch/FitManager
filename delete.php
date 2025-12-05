<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    require "connect.php";
    

    if (isset($_GET["delete_cours_id"])) {
        $delete_cours_id = $_GET["delete_cours_id"];

        $connect->query("delete from cours where id_cours = $delete_cours_id");

        header("Location: index.php");
        exit;
    }

    if (isset($_GET["delete_equipements_id"])) {
        $delete_equipements_id = $_GET["delete_equipements_id"];

        $connect->query("delete from equipements where id_equipements = $delete_equipements_id");

        header("Location: index.php");
        exit;
    }
?>