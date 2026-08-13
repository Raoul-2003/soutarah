<?php
// public/submit.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if (!isPost()) {
    redirect('/soutarah/public/index.php');
}

$token = $_POST['csrf_token'] ?? '';
$vehicleCode = $_POST['vehicle_code'] ?? '';

if (!verifyCsrfToken($token)) {
    die('Erreur de sécurité CSRF. Veuillez recommencer.');
}

if (empty($vehicleCode)) {
    die('Erreur : véhicule non spécifié.');
}

$vehicle = getVehicleByCode($pdo, $vehicleCode);
if (!$vehicle) {
    die('Erreur : véhicule introuvable.');
}

// Validation basique et récupération
$email = trim($_POST['email'] ?? '');
$withDriver = $_POST['with_driver'] ?? '';
$overallRating = (int)($_POST['overall_rating'] ?? 0);

// Vérification des champs requis avec astérisque
if (!$email || !$withDriver || !$overallRating) {
    flash('error', 'Veuillez remplir tous les champs obligatoires (*).', 'error');
    redirect('/soutarah/public/satisfaction.php?vehicle=' . urlencode($vehicleCode));
}

// Récupération des autres champs (facultatifs selon Google Forms)
$locationTypeParticulier = $_POST['location_type_particulier'] ?? null;
$locationTypeSociete = $_POST['location_type_societe'] ?? null;

// Tableaux de cases à cocher -> string
$cleanliness = isset($_POST['cleanliness']) ? implode(', ', $_POST['cleanliness']) : null;
$customerService = isset($_POST['customer_service']) ? implode(', ', $_POST['customer_service']) : null;
$driverPunct = isset($_POST['driver_punctuality']) ? implode(', ', $_POST['driver_punctuality']) : null;
$routeKnowledge = isset($_POST['route_knowledge']) ? implode(', ', $_POST['route_knowledge']) : null;

// Radios
$reservationCompliance = $_POST['reservation_compliance'] ?? null;
$technicalProblem = $_POST['technical_problem'] ?? null;
$waitingTime = $_POST['waiting_time'] ?? null;
$drivingQuality = $_POST['driving_quality'] ?? null;
$driverAttitude = $_POST['driver_attitude'] ?? null;

$comment = trim($_POST['comment'] ?? '');

try {
    $stmt = $pdo->prepare("
        INSERT INTO satisfaction_responses (
            vehicle_id, email, with_driver, 
            location_type_particulier, location_type_societe,
            cleanliness, reservation_compliance, technical_problem,
            customer_service, waiting_time, 
            driver_punctuality, driving_quality, driver_attitude, route_knowledge,
            overall_rating, comment, created_at
        ) VALUES (
            ?, ?, ?, 
            ?, ?,
            ?, ?, ?,
            ?, ?,
            ?, ?, ?, ?,
            ?, ?, NOW()
        )
    ");

    $stmt->execute([
        $vehicle['id'], $email, $withDriver,
        $locationTypeParticulier, $locationTypeSociete,
        $cleanliness, $reservationCompliance, $technicalProblem,
        $customerService, $waitingTime,
        $driverPunct, $drivingQuality, $driverAttitude, $routeKnowledge,
        $overallRating, $comment
    ]);

    redirect('/soutarah/public/thank-you.php');
} catch (PDOException $e) {
    error_log("Erreur d'insertion : " . $e->getMessage());
    flash('error', 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.', 'error');
    redirect('/soutarah/public/satisfaction.php?vehicle=' . urlencode($vehicleCode));
}
