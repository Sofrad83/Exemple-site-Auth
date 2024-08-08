<?php

use App\Database\DatabaseConfig;
use App\Database\DatabaseConnection;
use App\Database\DatabaseQuery;
use App\Support\Auth;
use App\Support\Route;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

//Base de données
$config = new DatabaseConfig();
// Connexion à la base de données
$dbConnection = new DatabaseConnection($config);
// Récupération de l'objet PDO
$pdo = $dbConnection->getPdo();
// Création de l'objet DatabaseQuery
$dbQuery = new DatabaseQuery($pdo);

//AUTH
Auth::init($dbQuery);

// Vérifier si l'utilisateur est connecté
if (Auth::isLoggedIn()) {
    // echo "Utilisateur connecté.";
} else {
    // echo "Utilisateur non connecté.";
}

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates/');
$twig = new Twig\Environment($loader, [
    'debug' => true,
    'cache' => false,
]);

$twig->addExtension(new \Twig\Extension\DebugExtension());

Route::init($app, $dbQuery);

//Liste des routes
include_once(__DIR__ . '/../routes/app.php');

$app->run();
