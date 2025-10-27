<?php



try {
   
include("../connexion-bases.php");
    $stmt = $connecter->query("SELECT academic_year_id, year_label 
                        FROM academic_years 
                        WHERE status = 'Actif'
                        LIMIT 1");
    $activeYear = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activeYear) {
        $_SESSION['academic_year_id'] = $activeYear['academic_year_id'];
        $_SESSION['academic_year_label'] = $activeYear['year_label'];
    } else {
        // Rediriger vers une page d’erreur ou de configuration
        header("Location: aucune_annee_active.php");
        exit;
    }
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

