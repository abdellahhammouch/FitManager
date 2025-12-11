<?php

require "connect.php";

$nom_cours = "";
$categories_cours = "";
$date_cours = "";
$heure_cours = "";
$duree_cours = "";
$max_participants = "";
$edit_cours_id = "";

$nom_equipements = "";
$type_equipements = "";
$quantity_equipements = "";
$etat_equipements = "";
$edit_equipement_id = "";


if (isset($_GET['edit_cours_id'])) {
    $edit_cours_id = intval($_GET['edit_cours_id']);
    $result_cours = $connect->query("select * from cours where id_cours = $edit_cours_id");
    $data_edit_cours = $result_cours->fetch_assoc();

    if ($data_edit_cours){
        $nom_cours = $data_edit_cours['nom_cours'];
        $categories_cours = $data_edit_cours['categories_cours'];
        $date_cours = $data_edit_cours['date_cours'];
        $heure_cours = $data_edit_cours['heure_cours'];
        $duree_cours = $data_edit_cours['duree_cours'];
        $max_participants = $data_edit_cours['max_participants'];
    }
}


if (isset($_POST['modifier'])) {
    $id_cours = intval($_POST["id_cours"]);
    $nom_cours = $_POST["nom_cours"];
    $categories_cours = $_POST["categories_cours"];
    $date_cours = $_POST["date_cours"];
    $heure_cours = $_POST["heure_cours"];
    $duree_cours = $_POST["duree_cours"];
    $max_participants = $_POST["max_participants"];

    $update = "update cours set
            nom_cours='$nom_cours',
            categories_cours='$categories_cours',
            date_cours='$date_cours',
            heure_cours='$heure_cours',
            duree_cours='$duree_cours',
            max_participants='$max_participants'  
            where id_cours=$id_cours";
    
    if ($connect->query($update)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error updating: " . $connect->error;
    }
}


if (isset($_GET['edit_equipements_id'])) {
    $edit_equipement_id = intval($_GET['edit_equipements_id']);
    $result_equipements = $connect->query("select * from equipements where id_equipements = $edit_equipement_id");
    $data_edit_equipements = $result_equipements->fetch_assoc();
    if($data_edit_equipements){
        $nom_equipements = $data_edit_equipements['nom_equipements'];
        $type_equipements = $data_edit_equipements['type_equipements'];
        $quantity_equipements = $data_edit_equipements['quantity_equipements'];
        $etat_equipements = $data_edit_equipements['etat_equipements'];
    }
}

if (isset($_POST['modifier_equipement'])) {
    $id_equipements = intval($_POST["id_equipements"]);
    $nom_equipements = $_POST["nom_equipements"];
    $type_equipements = $_POST["type_equipements"];
    $quantity_equipements = $_POST["quantity_equipements"];
    $etat_equipements = $_POST["etat_equipements"];

    $update = "update equipements set 
            nom_equipements='$nom_equipements',
            type_equipements='$type_equipements',
            quantity_equipements='$quantity_equipements',
            etat_equipements='$etat_equipements'  
            where id_equipements=$id_equipements";
    
    if ($connect->query($update)) {
        header("Location: index.php");
        exit();
    }else {
        echo "Error updating: " . $connect->error;
    }
}

?>