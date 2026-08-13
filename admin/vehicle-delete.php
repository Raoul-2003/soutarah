<?php
// admin/vehicle-delete.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

if (isPost()) {
    $token = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($token)) {
        flash('error', 'Erreur de sécurité CSRF.', 'error');
        redirect('/admin/vehicles.php');
    }

    $id = $_POST['id'] ?? null;
    if ($id) {
        try {
            // Optionnel : On pourrait vérifier s'il a des réponses avant de supprimer. 
            // Ici, si l'intégrité référentielle le permet (ON DELETE CASCADE) ça marchera, sinon ça plantera.
            $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
            $stmt->execute([$id]);
            flash('success', 'Véhicule supprimé avec succès.');
        } catch (PDOException $e) {
            flash('error', 'Erreur de base de données : impossible de supprimer ce véhicule. Il est peut-être lié à des réponses de satisfaction.', 'error');
        }
    }
}

redirect('/admin/vehicles.php');

