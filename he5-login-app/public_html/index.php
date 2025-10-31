<?php
// public_html/index.php
declare(strict_types=1);

// Handle root route redirect to avoid He5 Framework Router bug
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '') {
    header('Location: /login');
    exit;
}

// Include main application
include_once __DIR__ . '/../main.php';

/*
| He5 Framework Login Application — IEEE YESIST12
|
| © He5. All rights reserved.
| This software is proprietary and confidential.
| Unauthorized copying, distribution, or modification is strictly prohibited.
|
| Usage Terms:
| - Only authorized teams, employees, or partners may use this code.
| - Redistribution outside the permitted scope is forbidden.
| - Please contact He5 for permissions or licensing questions.
|
| Key Features:
| - Clean MVC architecture following He5 Framework patterns
| - Secure authentication with password hashing
| - RESTful API endpoints
| - Middleware-based access control
| - Comprehensive logging system
|
| Contact:
| Report issues or request access via the internal support channel.
|
*/
?>