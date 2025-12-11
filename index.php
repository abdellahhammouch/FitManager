<?php 
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include "edit.php";
    require "connect.php";
    $result_cours = $connect->query("select * from cours");
    $data_cours = $result_cours->fetch_assoc();
    $result_equipements = $connect->query("select * from equipements");
    $data_equipements = $result_equipements->fetch_assoc();
    $query_cours_par_categorie = "select categories_cours, COUNT(*) as nombre from cours group by categories_cours";
    $result_cours_par_categorie = $connect->query($query_cours_par_categorie);
    $count_cours = $connect->query("select count(id_cours) from cours");
    $data_count_cours = $count_cours->fetch_column();
    $count_equipements = $connect->query("select count(id_equipements) from equipements");
    $data_count_equipements = $count_equipements->fetch_column();
    $participants_totaux = $connect->query("select sum(max_participants) from cours");
    $data_participants = $participants_totaux->fetch_column();
    $equipements_disponibles = $connect->query("select count(nom_equipements) as equipements_disponibles from equipements where etat_equipements = 'Bon' or etat_equipements = 'Moyen'");
    $data_equipements_disponibles = $equipements_disponibles->fetch_column();

    // Créer un tableau avec toutes les catégories et initialiser à 0
    $categories_cours_data = [
        'Yoga' => 0,
        'Musculation' => 0,
        'Cardio' => 0,
        'Pilates' => 0,
        'CrossFit' => 0
    ];

    // Remplir avec les données réelles
    while ($row = $result_cours_par_categorie->fetch_assoc()) {
        $categories_cours_data[$row['categories_cours']] = $row['nombre'];
    }

    // Requête pour compter les équipements par type
    $query_equipements_par_type = "select type_equipements, sum(quantity_equipements) as total from equipements group by type_equipements";
    $result_equipements_par_type = $connect->query($query_equipements_par_type);

    // Créer un tableau avec tous les types et initialiser à 0
    $types_equipements_data = [
        'Tapis de course' => 0,
        'Haltères' => 0,
        'Ballons' => 0,
        'Vélo' => 0,
        'Rameur' => 0
    ];

    // Remplir avec les données réelles
    while ($row = $result_equipements_par_type->fetch_assoc()) {
        $types_equipements_data[$row['type_equipements']] = $row['total'];
    }

    // Calculer la valeur maximale pour normaliser les hauteurs des barres
    $max_cours = max($categories_cours_data);
    $max_equipements = max($types_equipements_data);
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitPro Manager - Gestion de Salle de Sport</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Impact', 'Arial Black', sans-serif;
            background: #1a1a1a;
            min-height: 100vh;
            padding: 0;
            color: #fff;
            margin: 0;
        }

        .container {
            max-width: 100%;
            margin: 0;
        }

        header {
            background: linear-gradient(180deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.7) 100%);
            backdrop-filter: blur(10px);
            border-radius: 0;
            padding: 30px 50px;
            margin-bottom: 0;
            border: none;
            border-bottom: 3px solid #ff6b00;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.8);
        }

        h1 {
            color: white;
            font-size: 3em;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            text-shadow: 3px 3px 0px #ff6b00, 6px 6px 20px rgba(255, 107, 0, 0.5);
        }

        h1 i {
            margin-right: 15px;
        }

        .nav-tabs {
            display: flex;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            color: #ccc;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 3px solid transparent;
        }

        .tab-btn i {
            margin-right: 8px;
        }

        .tab-btn:hover {
            background: rgba(255, 107, 0, 0.1);
            transform: none;
            box-shadow: none;
            color: #ff6b00;
            border-bottom-color: #ff6b00;
        }

        .tab-btn.active {
            background: rgba(255, 107, 0, 0.15);
            color: #ff6b00;
            border-bottom-color: #ff6b00;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 0;
            margin-bottom: 0;
            padding: 50px;
            background: #000;
        }

        .stat-card {
            background: #1a1a1a;
            border-radius: 12px;
            padding: 40px;
            box-shadow: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-right: 1px solid #333;
            border-bottom: 1px solid #333;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: #ff6b00;
        }

        .stat-card:hover {
            transform: none;
            box-shadow: none;
            background: #222;
        }

        .stat-number {
            font-size: 4em;
            font-weight: 900;
            color: #ff6b00;
            margin: 10px 0;
            font-family: 'Impact', sans-serif;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.5);
        }

        .stat-label {
            color: #999;
            font-size: 0.9em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .chart-container {
            background: #000;
            border-radius: 0;
            padding: 60px 50px;
            box-shadow: none;
            margin-bottom: 0;
            border-top: 1px solid #333;
        }

        .chart-title {
            font-size: 2em;
            font-weight: 900;
            color: #fff;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .chart-title i {
            margin-right: 15px;
        }

        .chart-bars {
            display: flex;
            gap: 10px;
            height: 350px;
            padding-top: 40px;
        }

        .bar-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .bar {
            width: 100%;
            background: linear-gradient(180deg, #ff6b00, #d45500);
            border-radius: 12px 12px 0 0;
            transition: all 0.3s ease;
            position: relative;
            cursor: pointer;
            box-shadow: 0 -5px 20px rgba(255, 107, 0, 0.3);
            min-width: 80px;
        }

        .bar:hover {
            transform: scaleY(1.05);
            box-shadow: 0 -8px 30px rgba(255, 107, 0, 0.6);
        }

        .bar-value {
            position: absolute;
            top: -40px;
            left: 50%;
            transform: translateX(-50%);
            font-weight: 900;
            color: #ff6b00;
            font-size: 2em;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        }

        .bar-label {
            font-size: 1.1em;
            color: #ccc;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 5px;
        }

        .form-section {
            background: #1a1a1a;
            border-radius: 0;
            padding: 50px;
            box-shadow: none;
            margin-bottom: 0;
            border-bottom: 1px solid #333;
        }

        .form-title {
            font-size: 2em;
            font-weight: 900;
            color: #fff;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form_group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-weight: 700;
            color: #999;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        input, select, textarea {
            padding: 15px;
            border: 2px solid #333;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: #000;
            color: #fff;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #ff6b00;
            box-shadow: 0 0 0 3px rgba(255, 107, 0, 0.2);
        }

        .btn {
            padding: 18px 45px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 900;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: none;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn-primary {
            background: #ff6b00;
            color: white;
            border: 3px solid #ff6b00;
        }

        .btn-primary:hover {
            transform: none;
            box-shadow: 0 0 30px rgba(255, 107, 0, 0.6);
            background: #ff8533;
        }

        .btn-secondary {
            background: transparent;
            color: #999;
            margin-left: 15px;
            border: 3px solid #333;
        }

        .btn-secondary:hover {
            background: #333;
            transform: none;
            color: #fff;
        }

        .table-container {
            background: #000;
            border-radius: 0;
            padding: 50px;
            box-shadow: none;
            overflow-x: auto;
            border-top: 1px solid #333;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead {
            background: #1a1a1a;
            border-bottom: 3px solid #ff6b00;
        }

        th {
            padding: 20px 15px;
            text-align: left;
            color: #ff6b00;
            font-weight: 900;
            font-size: 0.85em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        th:first-child {
            border-radius: 0;
        }

        th:last-child {
            border-radius: 0;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #222;
            color: #ccc;
        }

        tbody tr {
            transition: all 0.3s ease;
            background: #000;
        }

        tbody tr:hover {
            background: #1a1a1a;
        }

        .action-btns {
            display: flex;
            gap: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85em;
            font-weight: 900;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-edit {
            background: #ff6b00;
            color: white;
            border: 2px solid #ff6b00;
        }

        .btn-edit:hover {
            background: transparent;
            transform: none;
            color: #ff6b00;
        }

        .btn-delete {
            background: transparent;
            color: #dc3545;
            border: 2px solid #dc3545;
        }

        .btn-delete:hover {
            background: #dc3545;
            transform: none;
            color: white;
        }

        .badge {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.75em;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge-success {
            background: #28a745;
            color: white;
            border: 2px solid #28a745;
        }

        .badge-warning {
            background: #ffc107;
            color: #000;
            border: 2px solid #ffc107;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
            border: 2px solid #dc3545;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .icon {
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-dumbbell"></i> FitPro Manager</h1>
            <div class="nav-tabs">
                <button class="tab-btn active" onclick="showTab('dashboard')"><i class="fas fa-chart-line"></i> Dashboard</button>
                <button class="tab-btn" onclick="showTab('courses')"><i class="fas fa-running"></i> Gestion des Cours</button>
                <button class="tab-btn" onclick="showTab('equipment')"><i class="fas fa-cogs"></i> Gestion des Équipements</button>
                <button class="tab-btn" onclick="showTab('Association')"><i class="fas fa-cogs"></i> Gestion des Équipements</button>
            </div>
        </header>

        <!-- Dashboard -->
        <div id="dashboard" class="tab-content active">
            <div class="dashboard">
                <div class="stat-card">
                    <div class="stat-label">Total Cours</div>
                    <div class="stat-number" id="totalCourses"><?= $data_count_cours?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Équipements</div>
                    <div class="stat-number" id="totalEquipment"><?= $data_count_equipements?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Participants Totaux</div>
                    <div class="stat-number" id="totalParticipants"><?= $data_participants?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Équipements Disponibles</div>
                    <div class="stat-number" id="availableEquipment"><?= $data_equipements_disponibles?></div>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Répartition des Cours par Type</div>
                <div class="chart-bars" id="coursesChart">
                    <?php foreach ($categories_cours_data as $categorie => $nombre): ?>
                        <?php 
                            $height = $max_cours > 0 ? ($nombre / $max_cours) * 100 : 0;
                        ?>
                        <div class="bar-group">
                            <div class="bar" style="height: <?= $height ?>%">
                                <div class="bar-value"><?= $nombre ?></div>
                            </div>
                            <div class="bar-label"><?= $categorie ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="chart-container">
                <div class="chart-title"><i class="fas fa-tools"></i> Répartition des Équipements par Type</div>
                <div class="chart-bars" id="equipmentChart">
                    <?php foreach ($types_equipements_data as $type => $quantite): ?>
                        <?php 
                            $height = $max_equipements > 0 ? ($quantite / $max_equipements) * 100 : 0;
                        ?>
                        <div class="bar-group">
                            <div class="bar" style="height: <?= $height ?>%">
                                <div class="bar-value"><?= $quantite ?></div>
                            </div>
                            <div class="bar-label"><?= $type ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Gestion des Cours -->
        <div id="courses" class="tab-content">
            <div class="form-section">
                <div class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    <?= $edit_cours_id ? 'Modifier le Cours' : 'Ajouter un Nouveau Cours' ?>
                </div>
                <form id="courseForm" action="<?= $edit_cours_id ? 'edit.php' : 'form_handling1.php' ?>" method="POST">
                    <input type="hidden" name="id_cours" value="<?= $edit_cours_id ?>">
                <div class="form-grid">
                    <div class="form_group">
                        <label>Nom du Cours *</label>
                        <input type="text" name="nom_cours" id="courseName" value="<?= $nom_cours ?>" required>
                    </div>
                    <div class="form_group">
                        <label>Catégorie *</label>
                        <select id="courseCategory" name="categories_cours" required>
                            <option value="">Sélectionner...</option>
                            <option <?=($categories_cours == "Yoga") ? 'selected' : ''?> value="Yoga">Yoga</option>
                            <option <?=($categories_cours == "Musculation") ? 'selected' : ''?> value="Musculation">Musculation</option>
                            <option <?=($categories_cours == "Cardio") ? 'selected' : ''?> value="Cardio">Cardio</option>
                            <option <?=($categories_cours == "Pilates") ? 'selected' : ''?> value="Pilates">Pilates</option>
                            <option <?=($categories_cours == "CrossFit") ? 'selected' : ''?> value="CrossFit">CrossFit</option>
                        </select>
                    </div>
                    <div class="form_group">
                        <label>Date *</label>
                        <input type="date" name="date_cours" id="courseDate" value="<?= $date_cours ?>" required>
                    </div>
                    <div class="form_group">
                        <label>Heure *</label>
                        <input type="time" id="courseTime" name="heure_cours" value="<?= $heure_cours ?>" required>
                    </div>
                    <div class="form_group">
                        <label>Durée (minutes) *</label>
                        <input type="number" id="courseDuration" name="duree_cours" value="<?= $duree_cours ?>" min="15" required>
                    </div>
                    <div class="form_group">
                        <label>Participants Max *</label>
                        <input type="number" id="courseMaxParticipants" name="max_participants" value="<?= $max_participants ?>" min="1" required>
                    </div>
                </div>
                
                <?php if ($edit_cours_id){ ?>
                    <button type="submit" name="modifier" class="btn btn-primary">Mettre à Jour</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                <?php }else{ ?>
                    <button type="submit" class="btn btn-primary">Ajouter le Cours</button>
                    <button type="button" class="btn btn-secondary" onclick="resetCourseForm()">Annuler</button>
                <?php }; ?>
            </form>
        </div>

            <div class="table-container">
                <div class="form-title"><i class="fas fa-list"></i> Liste des Cours</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Durée</th>
                            <th>Max Participants</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="coursesTable">
                        <?php foreach($result_cours as $res) :?>
                            <tr>
                                <td><?php echo $res["nom_cours"];?></td>
                                <td><?php echo $res["categories_cours"];?></td>
                                <td><?php echo $res["date_cours"];?></td>
                                <td><?php echo $res["heure_cours"];?></td>
                                <td><?php echo $res["duree_cours"];?></td>
                                <td><?php echo $res["max_participants"];?></td>
                                <td class="action-btns">
                                        <a href="?edit_cours_id=<?= $res['id_cours']?>" name="modifier" class="btn-edit"><i class="fas fa-edit"></i> Modifier</a>
                                        <a href="delete.php?delete_cours_id=<?= $res['id_cours']?>" class="btn-delete"><i class="fas fa-trash"></i> Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gestion des Équipements -->
        <div id="equipment" class="tab-content">
            <div class="form-section">
                <div class="form-title">
                    <i class="fas fa-plus-circle"></i>
                    <?= $edit_equipement_id ? "Modifier l'Équipement" : "Ajouter un Nouvel Équipement" ?>
                </div>
                <form id="equipmentForm" action="<?= $edit_equipement_id ? 'edit.php' : 'form_handling2.php' ?>" method="post">
                    
                    <?php if ($edit_equipement_id){ ?>
                        <input type="hidden" name="id_equipements" value="<?= $edit_equipement_id ?>">
                    <?php }; ?>

                    <div class="form-grid">
                        <div class="form_group">
                            <label>Nom de l'Équipement *</label>
                            <input type="text" name="nom_equipements" id="equipmentName" value="<?= $nom_equipements ?>" required>
                        </div>
                        <div class="form_group">
                            <label>Type *</label>
                            <select id="equipmentType" name="type_equipements" required>
                                <option value="">Sélectionner...</option>
                                <option <?=($type_equipements == "Tapis de course") ? 'selected' : ''?> value="Tapis de course">Tapis de course</option>
                                <option <?=($type_equipements == "Haltères") ? 'selected' : ''?> value="Haltères">Haltères</option>
                                <option <?=($type_equipements == "Ballons") ? 'selected' : ''?> value="Ballons">Ballons</option>
                                <option <?=($type_equipements == "Vélo") ? 'selected' : ''?> value="Vélo">Vélo</option>
                                <option <?=($type_equipements == "Rameur") ? 'selected' : ''?> value="Rameur">Rameur</option>
                            </select>
                        </div>
                        <div class="form_group">
                            <label>Quantité Disponible *</label>
                            <input type="number" id="equipmentQuantity" name="quantity_equipements" value="<?= $quantity_equipements ?>" min="0" required>
                        </div>
                        <div class="form_group">
                            <label>État *</label>
                            <select id="equipmentState" name="etat_equipements" required>
                                <option value="">Sélectionner...</option>
                                <option <?=($etat_equipements == "Bon") ? 'selected' : ''?> value="Bon">Bon</option>
                                <option <?=($etat_equipements == "Moyen") ? 'selected' : ''?> value="Moyen">Moyen</option>
                                <option <?=($etat_equipements == "À remplacer") ? 'selected' : ''?> value="À remplacer">À remplacer</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if ($edit_equipement_id){ ?>
                        <button type="submit" name="modifier_equipement" class="btn btn-primary">Mettre à Jour</button>
                        <a href="index.php" class="btn btn-secondary">Annuler</a>
                    <?php }else{ ?>
                        <button type="submit" class="btn btn-primary">Ajouter l'Équipement</button>
                        <button type="button" class="btn btn-secondary" onclick="resetEquipmentForm()">Annuler</button>
                    <?php }; ?>
                </form>
            </div>

            <div class="table-container">
                <div class="form-title"><delete_equipements_idi class="fas fa-list"></delete_equipements_idi> Liste des Équipements</div>
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="equipmentTable">
                        <?php foreach($result_equipements as $res) :?>
                        <tr>
                            <td><?= $res["nom_equipements"];?></td>
                            <td><?= $res["type_equipements"];?></td>
                            <td><?= $res["quantity_equipements"];?></td>
                            <td><?= $res["etat_equipements"];?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="?edit_equipements_id=<?= $res['id_equipements']?>" class="btn-edit"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="delete.php?delete_equipements_id=<?= $res["id_equipements"]?>" class="btn-delete"><i class="fas fa-trash"></i> Supprimer</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>

        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
            
            if (tabName === 'dashboard') {
                updateDashboard();
            }
        }

        /* document.getElementById('courseForm').addEventListener('submit', function(e) {
            e.preventDefault();
        
            const course = {
                id: editingCourseId || Date.now(),
                name: document.getElementById('courseName').value,
                category: document.getElementById('courseCategory').value,
                date: document.getElementById('courseDate').value,
                time: document.getElementById('courseTime').value,
                duration: document.getElementById('courseDuration').value,
                maxParticipants: document.getElementById('courseMaxParticipants').value
            };

            if (editingCourseId) {
                const index = courses.findIndex(c => c.id === editingCourseId);
                courses[index] = course;
                editingCourseId = null;
            } else {
                courses.push(course);
            }

            resetCourseForm();
            renderCourses();
            updateDashboard();
        }); */

        /* document.getElementById('equipmentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const equip = {
                id: editingEquipmentId || Date.now(),
                name: document.getElementById('equipmentName').value,
                type: document.getElementById('equipmentType').value,
                quantity: document.getElementById('equipmentQuantity').value,
                state: document.getElementById('equipmentState').value
            };

            if (editingEquipmentId) {
                const index = equipment.findIndex(e => e.id === editingEquipmentId);
                equipment[index] = equip;
                editingEquipmentId = null;
            } else {
                equipment.push(equip);
            }

            resetEquipmentForm();
            renderEquipment();
            updateDashboard();
        }); */

        /* function renderCourses() {
            const tbody = document.getElementById('coursesTable');
            tbody.innerHTML = courses.map(course => `
                <tr>
                    <td>${course.name}</td>
                    <td>${course.category}</td>
                    <td>${course.date}</td>
                    <td>${course.time}</td>
                    <td>${course.duration} min</td>
                    <td>${course.maxParticipants}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-edit" onclick="editCourse(${course.id})"><i class="fas fa-edit"></i> Modifier</button>
                            <button class="btn-delete" onclick="deleteCourse(${course.id})"><i class="fas fa-trash"></i> Supprimer</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        } */

        /* function renderEquipment() {
            const tbody = document.getElementById('equipmentTable');
            tbody.innerHTML = equipment.map(equip => {
                let badgeClass = 'badge-success';
                if (equip.state === 'Moyen') badgeClass = 'badge-warning';
                if (equip.state === 'À remplacer') badgeClass = 'badge-danger';
                
                return `
                    <tr>
                        <td>${equip.name}</td>
                        <td>${equip.type}</td>
                        <td>${equip.quantity}</td>
                        <td><span class="badge ${badgeClass}">${equip.state}</span></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-edit" onclick="editEquipment(${equip.id})"><i class="fas fa-edit"></i> Modifier</button>
                                <button class="btn-delete" onclick="deleteEquipment(${equip.id})"><i class="fas fa-trash"></i> Supprimer</button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        } */

        /* function editCourse(id) {
            const course = courses.find(c => c.id === id);
            editingCourseId = id;
            
            document.getElementById('courseName').value = course.name;
            document.getElementById('courseCategory').value = course.category;
            document.getElementById('courseDate').value = course.date;
            document.getElementById('courseTime').value = course.time;
            document.getElementById('courseDuration').value = course.duration;
            document.getElementById('courseMaxParticipants').value = course.maxParticipants;
            
            document.querySelector('#courseForm .btn-primary').textContent = 'Mettre à Jour';
        } */

        /* function deleteCourse(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')) {
                courses = courses.filter(c => c.id !== id);
                renderCourses();
                updateDashboard();
            }
        } */

        /* function editEquipment(id) {
            const equip = equipment.find(e => e.id === id);
            editingEquipmentId = id;
            
            document.getElementById('equipmentName').value = equip.name;
            document.getElementById('equipmentType').value = equip.type;
            document.getElementById('equipmentQuantity').value = equip.quantity;
            document.getElementById('equipmentState').value = equip.state;
            
            document.querySelector('#equipmentForm .btn-primary').textContent = 'Mettre à Jour';
        } */

       /*  function deleteEquipment(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet équipement ?')) {
                equipment = equipment.filter(e => e.id !== id);
                renderEquipment();
                updateDashboard();
            }
        } */

        function resetCourseForm() {
            document.getElementById('courseForm').reset();
        }

        function resetEquipmentForm() {
            document.getElementById('equipmentForm').reset();
            document.querySelector('#equipmentForm .btn-primary').textContent = "Ajouter l'Équipement";
        }

        /* function updateDashboard() {
            document.getElementById('totalCourses').textContent = courses.length;
            document.getElementById('totalEquipment').textContent = equipment.length;
            
            const totalParticipants = courses.reduce((sum, c) => sum + parseInt(c.maxParticipants), 0);
            document.getElementById('totalParticipants').textContent = totalParticipants;
            
            const availableEq = equipment.reduce((sum, e) => sum + parseInt(e.quantity), 0);
            document.getElementById('availableEquipment').textContent = availableEq;
            
            renderCoursesChart();
            renderEquipmentChart();
        } */

        /* function renderCoursesChart() {
            const categories = {};
            courses.forEach(c => {
                categories[c.category] = (categories[c.category] || 0) + 1;
            });
            
            const container = document.getElementById('coursesChart');
            const maxValue = Math.max(...Object.values(categories), 1);
            
            container.innerHTML = Object.entries(categories).map(([cat, count]) => {
                const height = (count / maxValue) * 100;
                return `
                    <div class="bar-group">
                        <div class="bar" style="height: ${height}%">
                            <div class="bar-value">${count}</div>
                        </div>
                        <div class="bar-label">${cat}</div>
                    </div>
                `;
            }).join('');
        } */

        /* function renderEquipmentChart() {
            const types = {};
            equipment.forEach(e => {
                types[e.type] = (types[e.type] || 0) + parseInt(e.quantity);
            });
            
            const container = document.getElementById('equipmentChart');
            const maxValue = Math.max(...Object.values(types), 1);
            
            container.innerHTML = Object.entries(types).map(([type, qty]) => {
                const height = (qty / maxValue) * 100;
                return `
                    <div class="bar-group">
                        <div class="bar" style="height: ${height}%">
                            <div class="bar-value">${qty}</div>
                        </div>
                        <div class="bar-label">${type}</div>
                    </div>
                `;
            }).join('');
        } */

        /* updateDashboard(); */
    </script>
</body>
</html>