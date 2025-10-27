<?php
// Minimal He5 Framework Simulation for Login System

class He5Exception extends Exception {
    private int $httpCode;
    
    public function __construct(string $message = "Internal Server Error", int $code = 0, int $httpCode = 500) {
        parent::__construct($message, $code);
        $this->httpCode = $httpCode;
    }
    
    public function getHttpCode(): int {
        return $this->httpCode;
    }
}

class Logger {
    private string $logsPath;
    
    public function __construct(string $logsPath = "") {
        $this->logsPath = $logsPath;
    }
    
    public function info(string $message, array $context = []): void {
        $this->log('INFO', $message, $context);
    }
    
    public function error(string $message, array $context = []): void {
        $this->log('ERROR', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void {
        $this->log('WARNING', $message, $context);
    }
    
    private function log(string $level, string $message, array $context): void {
        if (empty($this->logsPath)) return;
        
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'context' => $context
        ];
        
        $logDir = __DIR__ . $this->logsPath . '/' . date('Y-m-d');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents($logDir . '/application.log', json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
}

class View {
    private string $viewsPath;
    
    public function __construct(string $viewsPath) {
        $this->viewsPath = $viewsPath;
    }
    
    public function render(string $template, array $data = []): void {
        extract($data);
        $templatePath = $this->viewsPath . '/' . $template . '.php';
        
        if (!file_exists($templatePath)) {
            throw new He5Exception("Template not found: $template", 404, 404);
        }
        
        include $templatePath;
    }
}

class He5 {
    public static function redirect(string $url, bool $newTab = false): void {
        if ($newTab) {
            echo "<script>window.open('$url', '_blank');</script>";
        } else {
            header("Location: $url");
            exit;
        }
    }
    
    public static function getCurrentUrl(): string {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
               "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
    
    public static function getParamValue(string $paramName): mixed {
        $input = json_decode(file_get_contents('php://input'), true);
        return $input[$paramName] ?? $_POST[$paramName] ?? $_GET[$paramName] ?? null;
    }
    
    public static function getParamArray(): array {
        $input = json_decode(file_get_contents('php://input'), true);
        return $input ?? $_POST ?? $_GET ?? [];
    }
}

interface MiddlewareInterface {
    public function handle(): bool;
}

class Param {
    public string $regex;
    public bool $required;
    public int $maxLength;
    
    public function __construct(string $regex, bool $required = false, int $maxLength = 255) {
        $this->regex = $regex;
        $this->required = $required;
        $this->maxLength = $maxLength;
    }
}

class Security {
    const STRING_REGEX = '/^[a-zA-Z0-9\s\-@._+]+$/';
    const EMAIL_REGEX = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    const PASSWORD_REGEX = '/^.{8,}$/';
    const INT_VALUE = '/^\d+$/';
    const MAIL_ID_LENGTH = 255;
}

class Router {
    private static ?Router $instance = null;
    private View $view;
    private Logger $logger;
    private PDO $dbConn;
    private array $routes = [];
    private string $requestMethod;
    private string $requestUri;
    
    public function __construct(string $viewsPath = "", Logger $logger = null) {
        $this->view = new View($viewsPath);
        $this->logger = $logger ?? new Logger();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        
        // Initialize database connection
        $this->initDatabase();
        
        // Set CORS headers
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-Token");
        
        if ($this->requestMethod === 'OPTIONS') {
            exit(0);
        }
        
        self::$instance = $this;
    }
    
    private function initDatabase(): void {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $this->dbConn = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            throw new He5Exception("Database connection failed: " . $e->getMessage(), 500, 500);
        }
    }
    
    public function addPage(string $path, $handler, array $middleware = []): void {
        $this->routes['GET'][$path] = ['handler' => $handler, 'middleware' => $middleware, 'params' => []];
    }
    
    public function post(string $path, array $handler, array $middleware = [], array $params = []): void {
        $this->routes['POST'][$path] = ['handler' => $handler, 'middleware' => $middleware, 'params' => $params];
    }
    
    public function get(string $path, array $handler, array $middleware = [], array $params = []): void {
        $this->routes['GET'][$path] = ['handler' => $handler, 'middleware' => $middleware, 'params' => $params];
    }
    
    public function close(): void {
        $this->handleRequest();
    }
    
    private function handleRequest(): void {
        try {
            $route = $this->findRoute($this->requestMethod, $this->requestUri);
            
            if (!$route) {
                throw new He5Exception("Route not found", 404, 404);
            }
            
            // Run middleware
            foreach ($route['middleware'] as $middleware) {
                if (is_object($middleware) && !$middleware->handle()) {
                    return;
                }
            }
            
            // Validate parameters
            $this->validateParameters($route['params']);
            
            // Execute handler
            $handler = $route['handler'];
            if (is_array($handler)) {
                $result = call_user_func($handler);
            } else {
                $result = $handler();
            }
            
            // Handle API responses
            if (is_array($result)) {
                header('Content-Type: application/json');
                echo json_encode($result);
            }
            
        } catch (He5Exception $e) {
            http_response_code($e->getHttpCode());
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getHttpCode()
            ]);
        } catch (Exception $e) {
            $this->logger->error("Unexpected error: " . $e->getMessage());
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Internal server error',
                'error_code' => 500
            ]);
        }
    }
    
    private function findRoute(string $method, string $uri): ?array {
        if (!isset($this->routes[$method])) {
            return null;
        }
        
        foreach ($this->routes[$method] as $pattern => $route) {
            if ($pattern === $uri) {
                return $route;
            }
        }
        
        return null;
    }
    
    private function validateParameters(array $params): void {
        foreach ($params as $paramName => $param) {
            $value = He5::getParamValue($paramName);
            
            if ($param->required && ($value === null || $value === '')) {
                throw new He5Exception("Required parameter missing: $paramName", 400, 400);
            }
            
            if ($value !== null && !preg_match($param->regex, $value)) {
                throw new He5Exception("Invalid parameter format: $paramName", 400, 400);
            }
            
            if ($value !== null && strlen($value) > $param->maxLength) {
                throw new He5Exception("Parameter too long: $paramName", 400, 400);
            }
        }
    }
    
    public static function getInstance(): Router {
        return self::$instance;
    }
    
    public static function LOGGER(): Logger {
        return self::$instance->logger;
    }
    
    public static function DB(): PDO {
        return self::$instance->dbConn;
    }
    
    public function getView(): View {
        return $this->view;
    }
    
    public function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    public function hasUserCredentials(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>