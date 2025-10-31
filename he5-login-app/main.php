<?php
// Include the PHAR file directly
require_once __DIR__ . '/He5-Frame-work-1.0.3.phar';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/src/Controllers/AuthController.php';
require_once __DIR__ . '/src/Middleware/AuthMiddleware.php';

// Initialize components
$logger = new Logger(__DIR__ . '/logs');

// Simple Router Implementation to bypass He5 Framework Router issues
class SimpleRouter {
    private $routes = [];
    private $logger;
    
    public function __construct($logger) {
        $this->logger = $logger;
    }
    
    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }
    
    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove trailing slash except for root
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }
        
        // Log the request (using error_log since Logger method is not standard)
        error_log("Handling $method request to $path");
        
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            
            if (is_array($handler)) {
                $controller = $handler[0];
                $method = $handler[1];
                return $controller->$method();
            } else if (is_callable($handler)) {
                return $handler();
            }
        }
        
        // 404 handling
        http_response_code(404);
        echo "404 - Page Not Found";
    }
    
    public static function DB() {
        static $db = null;
        if ($db === null) {
            // Try to use He5 Framework Router's database connection first
            try {
                if (class_exists('Router') && method_exists('Router', 'DB')) {
                    $db = Router::DB();
                    return $db;
                }
            } catch (Exception $e) {
                // Continue to PDO attempt
            }
            
            // Try direct PDO connection
            try {
                $db = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USERNAME,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception("Database connection failed: " . $e->getMessage() . ". Please ensure MySQL extension is enabled in PHP.");
            }
        }
        return $db;
    }
    
    public static function LOGGER() {
        static $logger = null;
        if ($logger === null) {
            $logger = new Logger(__DIR__ . '/logs');
        }
        return $logger;
    }
}

try {
    // Initialize Simple Router
    $router = new SimpleRouter($logger);
    
    // Define parameter validation patterns (removed Param::configure calls as they're not needed for our SimpleRouter)
    
    // Initialize AuthController
    $authController = new AuthController();
    
    // Define routes
    $router->get('/', [$authController, 'showLogin']);
    $router->get('/login', [$authController, 'showLogin']);
    $router->post('/login', [$authController, 'login']);
    $router->get('/signup', [$authController, 'showSignup']);
    $router->post('/signup', [$authController, 'signup']);
    $router->get('/dashboard', [$authController, 'showDashboard']);
    $router->post('/logout', [$authController, 'logout']);
    
    // API Routes
    $router->post('/api/login', [$authController, 'apiLogin']);
    $router->post('/api/signup', [$authController, 'apiSignup']);
    $router->post('/api/logout', [$authController, 'apiLogout']);
    
    // Handle the request
    $router->handleRequest();
    
} catch (Exception $e) {
    error_log('Application Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong']);
}