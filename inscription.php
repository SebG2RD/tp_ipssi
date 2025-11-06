<?php

// Charger la configuration depuis le fichier .env
require_once __DIR__ . '/config/config.php';

// Fonction simple pour renvoyer une réponse JSON
function renvoyerJson($donnees)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($donnees, JSON_UNESCAPED_UNICODE);
    exit;
}

// Connexion à la base de données en utilisant la fonction de config.php
try {
    $pdo = obtenirConnexionBDD();
} catch (PDOException $e) {
    renvoyerJson([
        'success' => false,
        'message' => 'Erreur de connexion à la base de données : ' . $e->getMessage()
    ]);
}

// Vérifier et créer la table si nécessaire (fonction définie dans config.php)
creerTableUtilisateurs($pdo);

// Vérifier que c'est bien une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    renvoyerJson([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
}

// Récupérer les données du formulaire
$email = trim($_POST['email'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');

// Tableau pour stocker les erreurs de validation
$erreurs = [];

// Validation de l'email
if (empty($email)) {
    $erreurs['email'] = "L'email est requis";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs['email'] = "L'email n'est pas valide";
} elseif (strlen($email) > 255) {
    $erreurs['email'] = "L'email est trop long (maximum 255 caractères)";
}

// Validation du nom
if (empty($nom)) {
    $erreurs['nom'] = "Le nom est requis";
} elseif (strlen($nom) < 2) {
    $erreurs['nom'] = "Le nom doit contenir au moins 2 caractères";
} elseif (strlen($nom) > 100) {
    $erreurs['nom'] = "Le nom est trop long (maximum 100 caractères)";
}

// Validation du prénom
if (empty($prenom)) {
    $erreurs['prenom'] = "Le prénom est requis";
} elseif (strlen($prenom) < 2) {
    $erreurs['prenom'] = "Le prénom doit contenir au moins 2 caractères";
} elseif (strlen($prenom) > 100) {
    $erreurs['prenom'] = "Le prénom est trop long (maximum 100 caractères)";
}

// Si il y a des erreurs de validation, on les renvoie
if (!empty($erreurs)) {
    renvoyerJson([
        'success' => false,
        'message' => 'Veuillez corriger les erreurs',
        'erreurs' => $erreurs
    ]);
}

// Vérifier si l'email existe déjà dans la base de données
try {
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        renvoyerJson([
            'success' => false,
            'message' => 'Vous êtes déjà inscrit',
            'erreurs' => ['email' => 'Votre email est déjà utilisé']
        ]);
    }

    // Insérer les données dans la base de données
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (email, nom, prenom) VALUES (?, ?, ?)");
    $stmt->execute([$email, $nom, $prenom]);

    // Tout est bon, on renvoie un succès
    renvoyerJson([
        'success' => true,
        'message' => "Inscription réussie ! Bienvenue $prenom $nom",
        'donnees' => [
            'email' => $email,
            'nom' => $nom,
            'prenom' => $prenom
        ]
    ]);
} catch (PDOException $e) {
    // Message d'erreur à afficher à l'utilisateur
    $messageErreur = 'Erreur lors de l\'enregistrement';

    // Log l'erreur complète dans un fichier (pour le débogage)
    error_log('Erreur inscription: ' . $e->getMessage());

    renvoyerJson([
        'success' => false,
        'message' => $messageErreur,
        'erreurs' => ['general' => $messageErreur]
    ]);
}
