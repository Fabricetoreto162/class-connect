<?php
include("../connexion-bases.php");

$etudiant = null;
$notes = [];
$message = "";
$matricule = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $matricule = trim($_POST["matricule"]);

    // 1️⃣ Vérifier si l'élève existe
    $sql = $connecter->prepare("SELECT * FROM students WHERE matricule = ?");
    $sql->execute([$matricule]);
    $etudiant = $sql->fetch(PDO::FETCH_ASSOC);

    if (!$etudiant) {
        $message = "❌ Aucun élève trouvé pour ce matricule.";
    } else {
        $student_id = $etudiant["student_id"];

        // 2️⃣ Récupérer le montant dû (département)
        $req = $connecter->prepare("
            SELECT d.amount AS total_du
            FROM students s
            JOIN levels l ON s.level_id = l.level_id
            JOIN departments d ON l.department_id = d.department_id
            WHERE s.student_id = ?
        ");
        $req->execute([$student_id]);
        $result = $req->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            $message = "⚠️ Impossible de déterminer le montant du filière car ce étudiant n’a pas de filière assigné.";
            $total_du = 0;
        } else {
            $total_du = intval($result["total_du"]);
        }
       

        // 3️⃣ Récupérer total payé
        $req2 = $connecter->prepare("
            SELECT COALESCE(SUM(amount),0) AS total_paye
            FROM payments
            WHERE student_id = ? AND types_paiement = 'Scolarité'
        ");
        $req2->execute([$student_id]);
        $res_pay = $req2->fetch(PDO::FETCH_ASSOC);
        $total_paye = intval($res_pay["total_paye"]);
        $reste = $total_du - $total_paye;

        if ($reste > 0) {
            $message = "⚠️ Accès refusé : Scolarité non soldée. Reste : " . number_format($reste, 0, ',', ' ') . " FCFA.";
            $etudiant = null;
        } else {
            // 4️⃣ Chargement des notes
            $notes_sql = $connecter->prepare("
                SELECT 
                    a.*,
                    s.subject_name,
                    s.coefficient,
                    ay.year_label
                FROM assignments a
                JOIN subjects s ON a.subject_id = s.subject_id
                JOIN academic_years ay ON a.academic_year_id = ay.academic_year_id
                WHERE a.student_id = ?
            ");
            $notes_sql->execute([$student_id]);
            $notes = $notes_sql->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Parents | Consultation des notes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --secondary: #7209b7;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --border-radius: 12px;
            --shadow: 0 8px 20px rgba(0,0,0,0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 6px solid var(--primary);
        }

        .header h1 {
            color: var(--primary);
            margin-bottom: 10px;
            font-size: 2.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Search Card */
        .search-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 600;
            font-size: 1rem;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        .btn-primary {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(67, 97, 238, 0.3);
        }

        /* Messages */
        .message {
            padding: 18px 20px;
            border-radius: 10px;
            margin: 20px 0;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .message-error {
            background-color: #ffebee;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .message-warning {
            background-color: #fff8e1;
            color: #e6a700;
            border-left: 4px solid var(--warning);
        }

        .message-success {
            background-color: #e8f5e9;
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        /* Student Info Card */
        .student-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            border-top: 5px solid var(--primary);
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .student-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
        }

        .student-info h3 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .student-info p {
            color: var(--gray);
            font-size: 1.1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .info-item {
            background: var(--light);
            padding: 15px;
            border-radius: 8px;
        }

        .info-item strong {
            color: var(--dark);
            display: block;
            margin-bottom: 5px;
        }

        .info-item span {
            color: var(--gray);
        }

        /* Notes Table */
        .table-container {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        thead {
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
        }

        thead th {
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
            transition: var(--transition);
        }

        tbody tr:hover {
            background-color: #f9f9f9;
        }

        tbody td {
            padding: 15px;
            color: #555;
        }

        .note-value {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
            text-align: center;
            display: inline-block;
            min-width: 50px;
        }

        .note-good {
            background-color: #d5f4e6;
            color: var(--success);
        }

        .note-medium {
            background-color: #fff3cd;
            color: #e6a700;
        }

        .note-poor {
            background-color: #f8d7da;
            color: var(--danger);
        }

        .appreciation {
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .appreciation-excellent {
            background-color: #d5f4e6;
            color: var(--success);
        }

        .appreciation-good {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .appreciation-average {
            background-color: #fff3cd;
            color: #856404;
        }

        .appreciation-poor {
            background-color: #f8d7da;
            color: var(--danger);
        }

        .semester-badge {
            background-color: #e0e7ff;
            color: var(--primary);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Stats */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }

        .stat-icon.average {
            background-color: #e0f7fa;
            color: #00838f;
        }

        .stat-icon.subjects {
            background-color: #f3e5f5;
            color: #8e24aa;
        }

        .stat-icon.coefficient {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .stat-info h4 {
            color: var(--dark);
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        /* Nouveau style pour les notes non-disponibles */
        .note-unavailable {
            background-color: #f0f0f0;
            color: #666;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-style: italic;
            display: inline-block;
            text-align: center;
            max-width: 150px;
        }

        /* Footer */
        .footer {
            text-align: center;
            color: var(--gray);
            padding: 20px;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            margin-top: 40px;
        }

        .footer a {
            color: var(--primary);
            text-decoration: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header h1 {
                font-size: 1.8rem;
            }
            
            .student-header {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .note-unavailable {
                font-size: 0.75rem;
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-user-graduate"></i> Espace Parents</h1>
            <p>Consultez les notes et le bulletin scolaire de votre enfant en toute sécurité</p>
        </div>
       

        <!-- Search Form -->
        <div class="search-card">
            <h2 style="color: var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-search"></i> Rechercher un élève
            </h2>
            <form method="POST">
                <div class="form-group">
                    <label for="matricule">Numéro matricule de l'élève</label>
                    <div class="input-with-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" id="matricule" name="matricule" placeholder="Ex: CON/2025/XXX" required value="<?= htmlspecialchars($matricule) ?>">
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i> Consulter les notes
                </button>
            </form>
        </div>

        <!-- Messages -->
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '❌') !== false ? 'message-error' : (strpos($message, '⚠️') !== false ? 'message-warning' : 'message-success'); ?>">
                <i class="<?php echo strpos($message, '❌') !== false ? 'fas fa-times-circle' : (strpos($message, '⚠️') !== false ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle'); ?>"></i>
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if ($etudiant && $notes): ?>
            <!-- Student Info -->
            <div class="student-card">
                <div class="student-header">
                    <div class="student-avatar">
                        <?= substr($etudiant["first_name"], 0, 1) . substr($etudiant["last_name"], 0, 1) ?>
                    </div>
                    <div class="student-info">
                        <h3><?= htmlspecialchars($etudiant["first_name"] . " " . $etudiant["last_name"]) ?></h3>
                        <p><i class="fas fa-id-card"></i> Matricule : <?= htmlspecialchars($etudiant["matricule"]) ?></p>
                    </div>
                </div>
                
                <?php
                $totalMoyenne = 0;
                $totalCoefficient = 0;
                $notesCompletes = 0;
                $notesPositives = 0;

                foreach ($notes as &$n) {
                    // Vérifier si toutes les notes sont disponibles ET supérieures à 0
                    $toutesNotesDisponibles = (
                        !is_null($n["assignment1"]) &&
                        !is_null($n["assignment2"]) &&
                        !is_null($n["assignment3"]) &&
                        !is_null($n["exam1"]) &&
                        !is_null($n["exam2"])
                    );

                    $toutesNotesPositives = false;
                    $moyenne_calculee = "";
                    
                    if ($toutesNotesDisponibles) {
                        // Vérifier si toutes les notes sont supérieures à 0
                        $A1 = floatval($n["assignment1"]);
                        $A2 = floatval($n["assignment2"]);
                        $A3 = floatval($n["assignment3"]);
                        $E1 = floatval($n["exam1"]);
                        $E2 = floatval($n["exam2"]);

                        if ($A1 > 0 && $A2 > 0 && $A3 > 0 && $E1 > 0 && $E2 > 0) {
                            $toutesNotesPositives = true;
                            
                            // Calculer la moyenne
                            $moy_assign = ($A1 + $A2 + $A3) / 3;
                            $moyenne = ($moy_assign + ($E1 + $E2)) / 3;
                            
                            // Ajouter à la moyenne générale
                            $totalMoyenne += $moyenne * $n["coefficient"];
                            $totalCoefficient += $n["coefficient"];
                            $notesCompletes++;
                            $notesPositives++;
                            
                            $moyenne_calculee = round($moyenne, 2);
                        } else {
                            $moyenne_calculee = "Moyenne non disponible (notes ≤ 0)";
                        }
                    } else {
                        $moyenne_calculee = "Moyenne non disponible (notes manquantes)";
                    }
                    
                    $n["moyenne_calculee"] = $moyenne_calculee;
                    $n["toutesNotesPositives"] = $toutesNotesPositives;
                    $n["toutesNotesDisponibles"] = $toutesNotesDisponibles;
                }

                $moyenneGenerale = ($totalCoefficient > 0 && $notesPositives > 0) 
                                  ? $totalMoyenne / $totalCoefficient 
                                  : 0;
                ?>

                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon average">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Moyenne Générale</h4>
                            <div class="stat-value">
                                <?php if ($notesPositives > 0): ?>
                                    <?= number_format($moyenneGenerale, 2) ?>/20
                                <?php else: ?>
                                    N/D
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon subjects">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Matières évaluées</h4>
                            <div class="stat-value"><?= count($notes) ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon coefficient">
                            <i class="fas fa-weight-hanging"></i>
                        </div>
                        <div class="stat-info">
                            <h4>Moyennes disponibles</h4>
                            <div class="stat-value"><?= $notesPositives ?>/<?= count($notes) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Table -->
            <div class="table-container">
                <h3 style="padding: 20px; color: var(--primary); border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-clipboard-list"></i> Bulletin détaillé
                </h3>
                <table>
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Année</th>
                            <th>Semestre</th>
                            <th>Int. 1</th>
                            <th>Int. 2</th>
                            <th>Int. 3</th>
                            <th>Dev. 1</th>
                            <th>Dev. 2</th>
                            <th>Moyenne</th>
                            <th>Coeff</th>
                            <th>Date</th>
                            <th>Appréciation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notes as $n): ?>
                            <?php
                            // Vérifier si on peut calculer la moyenne
                            $peutCalculerMoyenne = $n["toutesNotesDisponibles"] && $n["toutesNotesPositives"];
                            
                            if ($peutCalculerMoyenne) {
                                $A1 = floatval($n["assignment1"]);
                                $A2 = floatval($n["assignment2"]);
                                $A3 = floatval($n["assignment3"]);
                                $E1 = floatval($n["exam1"]);
                                $E2 = floatval($n["exam2"]);
                                
                                $moy_assign = ($A1 + $A2 + $A3) / 3;
                                $moyenne = ($moy_assign + ($E1 + $E2)) / 3;
                                $moyenne = round($moyenne, 2);
                                
                                // Déterminer la classe CSS pour la note
                                $noteClass = '';
                                if ($moyenne >= 15) {
                                    $app = "Très bien";
                                    $appClass = "appreciation-excellent";
                                    $noteClass = "note-good";
                                } elseif ($moyenne >= 12) {
                                    $app = "Bien";
                                    $appClass = "appreciation-good";
                                    $noteClass = "note-good";
                                } elseif ($moyenne >= 10) {
                                    $app = "Passable";
                                    $appClass = "appreciation-average";
                                    $noteClass = "note-medium";
                                } else {
                                    $app = "Insuffisant";
                                    $appClass = "appreciation-poor";
                                    $noteClass = "note-poor";
                                }
                            } else {
                                $moyenne = $n["moyenne_calculee"];
                                $app = "Non évalué";
                                $appClass = "";
                                $noteClass = "";
                            }
                            
                            $semestre = ($n["note_date"] >= "2025-01-01") ? "Semestre 2" : "Semestre 1";
                            ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($n["subject_name"]) ?></strong></td>
                                <td><?= htmlspecialchars($n["year_label"]) ?></td>
                                <td><span class="semester-badge"><?= $semestre ?></span></td>
                                <td>
                                    <?php if (is_null($n["assignment1"])): ?>
                                        <span style="color:#aaa;">N/A</span>
                                    <?php else: ?>
                                        <span class="note-value <?= floatval($n["assignment1"]) > 0 ? '' : 'note-poor' ?>">
                                            <?= number_format($n["assignment1"], 1) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (is_null($n["assignment2"])): ?>
                                        <span style="color:#aaa;">N/A</span>
                                    <?php else: ?>
                                        <span class="note-value <?= floatval($n["assignment2"]) > 0 ? '' : 'note-poor' ?>">
                                            <?= number_format($n["assignment2"], 1) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (is_null($n["assignment3"])): ?>
                                        <span style="color:#aaa;">N/A</span>
                                    <?php else: ?>
                                        <span class="note-value <?= floatval($n["assignment3"]) > 0 ? '' : 'note-poor' ?>">
                                            <?= number_format($n["assignment3"], 1) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (is_null($n["exam1"])): ?>
                                        <span style="color:#aaa;">N/A</span>
                                    <?php else: ?>
                                        <span class="note-value <?= floatval($n["exam1"]) > 0 ? '' : 'note-poor' ?>">
                                            <?= number_format($n["exam1"], 1) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (is_null($n["exam2"])): ?>
                                        <span style="color:#aaa;">N/A</span>
                                    <?php else: ?>
                                        <span class="note-value <?= floatval($n["exam2"]) > 0 ? '' : 'note-poor' ?>">
                                            <?= number_format($n["exam2"], 1) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($peutCalculerMoyenne && is_numeric($moyenne)): ?>
                                        <span class="note-value <?= $noteClass ?>"><?= number_format($moyenne, 2) ?></span>
                                    <?php else: ?>
                                        <span class="note-unavailable"><?= $moyenne ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="note-value" style="background:#e9ecef;color:#495057;"><?= htmlspecialchars($n["coefficient"]) ?></span></td>
                                <td><?= htmlspecialchars(date("d/m/Y", strtotime($n["note_date"]))) ?></td>
                                <td>
                                    <?php if ($peutCalculerMoyenne): ?>
                                        <span class="appreciation <?= $appClass ?>"><?= $app ?></span>
                                    <?php else: ?>
                                        <span class="appreciation" style="background:#f0f0f0;color:#666;">Non évalué</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Additional Information -->
            <div style="background: white; padding: 20px; border-radius: var(--border-radius); box-shadow: var(--shadow); margin-bottom: 30px;">
                <h4 style="color: var(--primary); margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-info-circle"></i> Informations complémentaires
                </h4>
                <div class="info-grid">
                    <div class="info-item">
                        <strong><i class="fas fa-calendar-check"></i> Date de consultation</strong>
                        <span><?= date("d/m/Y à H:i") ?></span>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-check-circle"></i> Statut des paiements</strong>
                        <span style="color: var(--success); font-weight: 600;">Scolarité soldée</span>
                    </div>
                    <div class="info-item">
                        <strong><i class="fas fa-graduation-cap"></i> Moyennes calculables</strong>
                        <span><?= $notesPositives ?> / <?= count($notes) ?> matières</span>
                    </div>
                </div>
                
                <?php if ($notesPositives < count($notes)): ?>
                    <div style="margin-top: 20px; padding: 15px; background-color: #fff8e1; border-radius: 8px; border-left: 4px solid #f39c12;">
                        <p style="margin: 0; color: #856404; font-size: 0.95rem;">
                            <i class="fas fa-info-circle"></i> <strong>Note :</strong> 
                            Certaines moyennes ne sont pas disponibles car toutes les notes nécessaires ne sont pas renseignées ou sont égales à 0.
                            Veuillez contacter l'administration pour plus d'informations.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($etudiant && !$notes): ?>
            <!-- Payment Information Only -->
            <div class="student-card">
                <div class="student-header">
                    <div class="student-avatar">
                        <?= substr($etudiant["first_name"], 0, 1) . substr($etudiant["last_name"], 0, 1) ?>
                    </div>
                    <div class="student-info">
                        <h3><?= htmlspecialchars($etudiant["first_name"] . " " . $etudiant["last_name"]) ?></h3>
                        <p><i class="fas fa-id-card"></i> Matricule : <?= htmlspecialchars($etudiant["matricule"]) ?></p>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <h4 style="color: var(--dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-file-invoice-dollar"></i> Situation financière
                    </h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <strong>Total dû</strong>
                            <span style="color: var(--dark); font-weight: 600;"><?= number_format($total_du, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <div class="info-item">
                            <strong>Total payé</strong>
                            <span style="color: var(--success); font-weight: 600;"><?= number_format($total_paye, 0, ',', ' ') ?> FCFA</span>
                        </div>
                        <div class="info-item">
                            <strong>Reste à payer</strong>
                            <span style="color: var(--danger); font-weight: 600;"><?= number_format($reste, 0, ',', ' ') ?> FCFA</span>
                        </div>
                    </div>
                    
                    <?php if ($reste > 0): ?>
                        <div class="message message-warning" style="margin-top: 25px;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Bulletin non disponible</strong><br>
                                Le bulletin scolaire ne peut être consulté que lorsque la scolarité est entièrement soldée.
                            </div>
                        </div>
                        
                        <div style="background: #fff8e1; padding: 20px; border-radius: 10px; margin-top: 20px; border-left: 4px solid #ffc107;">
                            <h5 style="color: #856404; margin-bottom: 10px;">
                                <i class="fas fa-lightbulb"></i> Comment régulariser la situation ?
                            </h5>
                            <p style="color: #856404; margin-bottom: 5px;">1. Contactez le service financier de l'établissement</p>
                            <p style="color: #856404; margin-bottom: 5px;">2. Effectuez le paiement du solde restant</p>
                            <p style="color: #856404;">3. Revenez consulter le bulletin après régularisation</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p><i class="fas fa-shield-alt"></i> Espace sécurisé - Consultation strictement réservée aux parents</p>
            <p>© <?= date("Y") ?> Class <span class="text-warning" style="font-family: cubic;">Connect</span> - Tous droits réservés</p>
        </div>
    </div>

    <script>
        // Animation simple pour les statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const statValues = document.querySelectorAll('.stat-value');
            statValues.forEach(stat => {
                const originalText = stat.textContent;
                
                // Ne pas animer si c'est "N/D"
                if (originalText === 'N/D' || originalText.includes('/') && originalText.split('/')[0] === '0') {
                    return;
                }
                
                stat.textContent = '0';
                
                let counter = 0;
                let target;
                
                if (originalText.includes('/')) {
                    const parts = originalText.split('/');
                    target = parseFloat(parts[0].replace(',', '.'));
                } else {
                    target = parseFloat(originalText.replace(',', '.'));
                }
                
                // Si target n'est pas un nombre valide, on arrête
                if (isNaN(target)) return;
                
                const increment = target / 50;
                const timer = setInterval(() => {
                    counter += increment;
                    if (counter >= target) {
                        stat.textContent = originalText;
                        clearInterval(timer);
                    } else {
                        if (originalText.includes('/')) {
                            const parts = originalText.split('/');
                            stat.textContent = counter.toFixed(2) + '/' + parts[1];
                        } else {
                            stat.textContent = Math.round(counter);
                        }
                    }
                }, 30);
            });
        });
    </script>
</body>
</html>