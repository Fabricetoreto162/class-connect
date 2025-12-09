<?php
header('Content-Type: application/json; charset=utf-8');
include('../connexion-bases.php');

if (!isset($_GET['student_id']) || empty($_GET['student_id'])) {
    echo json_encode([]);
    exit;
}

$student_id = intval($_GET['student_id']);


// --------------------------------------------------
// 1️⃣ RÉCUPÉRER LES PAIEMENTS DE L'ÉTUDIANT
// --------------------------------------------------

$sql = $connecter->prepare("
    SELECT 
        payment_id,
        DATE_FORMAT(payment_date, '%d/%m/%Y') AS payment_date,
        amount,
        types_paiement,
        method
    FROM payments
    WHERE student_id = ?
    ORDER BY payment_date DESC
");
$sql->execute([$student_id]);
$payments = $sql->fetchAll(PDO::FETCH_ASSOC);


// --------------------------------------------------
// 2️⃣ TOTAL PAYÉ
// --------------------------------------------------

$total_paye = 0;
foreach ($payments as $p) {
    $total_paye += intval($p['amount']);
}


// --------------------------------------------------
// 3️⃣ TOTAL DÛ (vrai montant issu de LEVEL → DEPARTMENT)
// --------------------------------------------------

$amount_infos = $connecter->prepare("
    SELECT 
        d.amount AS total_due
    FROM students s
    JOIN levels l ON s.level_id = l.level_id
    JOIN departments d ON l.department_id = d.department_id
    WHERE s.student_id = ?
");
$amount_infos->execute([$student_id]);
$result = $amount_infos->fetch(PDO::FETCH_ASSOC);

$total_du = $result ? intval($result['total_due']) : 0;


// --------------------------------------------------
// 4️⃣ RESTE À PAYER
// --------------------------------------------------

$reste = max($total_du - $total_paye, 0);  
// max() pour éviter les montants négatifs


// --------------------------------------------------
// 5️⃣ AJOUTER UNE RÉFÉRENCE À CHAQUE PAIEMENT
// --------------------------------------------------

foreach ($payments as &$p) {
    $p['reference'] = "REC-" . date("Y") . "-" . str_pad($p['payment_id'], 4, "0", STR_PAD_LEFT);
}


// --------------------------------------------------
// 6️⃣ EXPORT JSON
// --------------------------------------------------

echo json_encode([
    "total_paye" => $total_paye,
    "total_du"   => $total_du,
    "reste"      => $reste,
    "paiements"  => $payments
]);

exit;
