<?php
// HE5 INTERNAL & SERVICES USE - He5 Framework
// Main Application Entry Point

// Include framework and configuration
require_once __DIR__ . "/He5Framework.php";
require_once __DIR__ . "/config.php";

// Include controllers and middleware
require_once __DIR__ . "/src/Controllers/AuthController.php";
require_once __DIR__ . "/src/Middleware/AuthMiddleware.php";

// Initialize logger
$logger = new Logger(LOGS_PATH);

// Create router instance
$router = new Router(__DIR__ . "/src/Views", $logger);

// Create controller instances
$authController = new AuthController();

// Create middleware instances
$pageAuth = new PageAuthMiddleware();
$apiAuth = new ApiAuthMiddleware();

// Define parameter validation
$signupParams = [
    'email' => new Param(Security::EMAIL_REGEX, true, Security::MAIL_ID_LENGTH),
    'password' => new Param(Security::PASSWORD_REGEX, true, 100),
    'fullName' => new Param(Security::STRING_REGEX, true, 100),
    'phone' => new Param(Security::STRING_REGEX, true, 20)
];

$signinParams = [
    'email' => new Param(Security::EMAIL_REGEX, true, Security::MAIL_ID_LENGTH),
    'password' => new Param(Security::PASSWORD_REGEX, true, 100)
];

// Page Routes (render HTML)
$router->addPage("/", [$authController, 'loginPage']);
$router->addPage("/login", [$authController, 'loginPage']);
$router->addPage("/signup", [$authController, 'loginPage']);  // Same page with tabs
$router->addPage("/dashboard", [$authController, 'dashboardPage'], [$pageAuth]);

// API Routes (return JSON)
$router->post('/api/signup', [$authController, 'signup'], [], $signupParams);
$router->post('/api/signin', [$authController, 'signin'], [], $signinParams);
$router->post('/api/logout', [$authController, 'logout'], [$apiAuth]);
$router->get('/api/profile', [$authController, 'getProfile'], [$apiAuth]);

// Handle the request
$router->close();
?>