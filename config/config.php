<?php

// Récupérer les variables d'environnement avec des valeurs par défaut
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'tp_php');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8');

// connexion à la base de données
function obtenirConnexionBDD()
{
    // Créer la chaîne de connexion (DSN = Data Source Name)
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    // Créer une nouvelle connexion PDO avec les identifiants
    $pdo = new PDO($dsn, DB_USER, DB_PASS);

    // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retourner l'objet de connexion
    return $pdo;
}

function chargerEnv($cheminFichier = __DIR__ . '/../.env')
{
    // Vérifier si le fichier .env existe
    if (!file_exists($cheminFichier)) {
        error_log("Attention : Le fichier .env n'existe pas à l'emplacement : $cheminFichier");
        return false;
    }

    // Lire le fichier ligne par ligne
    $lignes = file($cheminFichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lignes as $ligne) {
        if (strpos($ligne, '=') !== false) {
            list($cle, $valeur) = explode('=', $ligne, 2);
            $cle = trim($cle);
            $valeur = trim($valeur);

            // Supprimer les guillemets si présents
            $valeur = trim($valeur, '"\'');

            // Définir la variable d'environnement si elle n'existe pas déjà
            if (!empty($cle) && !isset($_ENV[$cle])) {
                $_ENV[$cle] = $valeur;
                putenv("$cle=$valeur");
            }
        }
    }

    return true;
}

// Charger le fichier .env au démarrage
chargerEnv();

// création de la table utilisateurs
function creerTableUtilisateurs($pdo)
{
    $sql = "CREATE TABLE IF NOT EXISTS `utilisateurs` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `email` VARCHAR(255) NOT NULL,
        `nom` VARCHAR(100) NOT NULL,
        `prenom` VARCHAR(100) NOT NULL,
        `date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `email_unique` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    try {
        // Exécuter la requête SQL pour créer la table
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        // En cas d'erreur, l'enregistrer dans les logs et retourner false
        error_log('Erreur création table: ' . $e->getMessage());
        return false;
    }
}
