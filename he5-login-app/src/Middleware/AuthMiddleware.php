<?php
// Authentication Middleware for Pages
class PageAuthMiddleware implements MiddlewareInterface {
    public function handle(): bool {
        // Use the framework's hasUserCredentials method to check authentication
        if (!Router::getInstance()->hasUserCredentials()) {
            Router::LOGGER()->info("Unauthenticated page access attempt, redirecting to login");
            He5::redirect(SITE_PATH . "/login?redirect=" . urlencode(He5::getCurrentUrl()));
            return false;
        }
        
        Router::LOGGER()->info("Page access authorized", ['user_id' => Router::getInstance()->getUserId()]);
        return true;
    }
}

// Authentication Middleware for API
class ApiAuthMiddleware implements MiddlewareInterface {
    public function handle(): bool {
        // Use the framework's hasUserCredentials method to check authentication
        if (!Router::getInstance()->hasUserCredentials()) {
            Router::LOGGER()->warning("Unauthenticated API access attempt");
            throw new He5Exception("Access denied. User not logged in.", 4001, 401);
        }
        
        Router::LOGGER()->info("API access authorized", ['user_id' => Router::getInstance()->getUserId()]);
        return true;
    }
}
?>