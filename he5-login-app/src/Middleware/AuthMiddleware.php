<?php
// Authentication Middleware for Pages
class PageAuthMiddleware implements MiddlewareInterface {
    public function handle(): bool {
        if (!Router::getInstance()->hasUserCredentials()) {
            He5::redirect(SITE_PATH . "/login?redirect=" . urlencode(He5::getCurrentUrl()));
            return false;
        }
        return true;
    }
}

// Authentication Middleware for API
class ApiAuthMiddleware implements MiddlewareInterface {
    public function handle(): bool {
        if (!Router::getInstance()->hasUserCredentials()) {
            throw new He5Exception("Access denied. User not logged in.", 401, 401);
        }
        return true;
    }
}
?>