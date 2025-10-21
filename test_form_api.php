<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database configuration
$host = 'localhost';
$username = 'root';
$password = 'root';
$database = 'formbuilder';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [];

switch($method) {
    case 'GET':
        if (empty($request)) {
            // Get all form templates
            getAllFormTemplates($pdo);
        } elseif ($request[0] == 'template' && isset($request[1])) {
            // Get specific form template
            getFormTemplate($pdo, $request[1]);
        } elseif ($request[0] == 'entries' && isset($request[1])) {
            // Get form entries for a specific form
            getFormEntries($pdo, $request[1]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($request)) {
            // Create new form template
            createFormTemplate($pdo, $input);
        } elseif ($request[0] == 'entry') {
            // Create form entry
            createFormEntry($pdo, $input);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;
        
    case 'PUT':
        if ($request[0] == 'template' && isset($request[1])) {
            $input = json_decode(file_get_contents('php://input'), true);
            updateFormTemplate($pdo, $request[1], $input);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;
        
    case 'DELETE':
        if ($request[0] == 'template' && isset($request[1])) {
            deleteFormTemplate($pdo, $request[1]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

function getAllFormTemplates($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM form_templates ORDER BY created_at DESC");
        $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON structure for each template
        foreach ($templates as &$template) {
            $template['structure'] = json_decode($template['structure'], true);
        }
        
        echo json_encode($templates);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching templates: ' . $e->getMessage()]);
    }
}

function getFormTemplate($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM form_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($template) {
            $template['structure'] = json_decode($template['structure'], true);
            echo json_encode($template);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching template: ' . $e->getMessage()]);
    }
}

function createFormTemplate($pdo, $data) {
    try {
        if (!isset($data['title']) || !isset($data['structure'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and structure are required']);
            return;
        }
        
        $stmt = $pdo->prepare("INSERT INTO form_templates (title, structure) VALUES (?, ?)");
        $stmt->execute([$data['title'], json_encode($data['structure'])]);
        
        $id = $pdo->lastInsertId();
        echo json_encode([
            'id' => $id,
            'title' => $data['title'],
            'structure' => $data['structure'],
            'message' => 'Form template created successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating template: ' . $e->getMessage()]);
    }
}

function updateFormTemplate($pdo, $id, $data) {
    try {
        if (!isset($data['title']) || !isset($data['structure'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Title and structure are required']);
            return;
        }
        
        $stmt = $pdo->prepare("UPDATE form_templates SET title = ?, structure = ? WHERE id = ?");
        $stmt->execute([$data['title'], json_encode($data['structure']), $id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode([
                'id' => $id,
                'title' => $data['title'],
                'structure' => $data['structure'],
                'message' => 'Form template updated successfully'
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error updating template: ' . $e->getMessage()]);
    }
}

function deleteFormTemplate($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM form_templates WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['message' => 'Form template deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Template not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error deleting template: ' . $e->getMessage()]);
    }
}

function createFormEntry($pdo, $data) {
    try {
        if (!isset($data['form_id']) || !isset($data['data'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Form ID and data are required']);
            return;
        }
        
        $stmt = $pdo->prepare("INSERT INTO form_entries (form_id, data) VALUES (?, ?)");
        $stmt->execute([$data['form_id'], json_encode($data['data'])]);
        
        $id = $pdo->lastInsertId();
        echo json_encode([
            'id' => $id,
            'form_id' => $data['form_id'],
            'data' => $data['data'],
            'message' => 'Form entry created successfully'
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating entry: ' . $e->getMessage()]);
    }
}

function getFormEntries($pdo, $form_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM form_entries WHERE form_id = ? ORDER BY created_at DESC");
        $stmt->execute([$form_id]);
        $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Decode JSON data for each entry
        foreach ($entries as &$entry) {
            $entry['data'] = json_decode($entry['data'], true);
        }
        
        echo json_encode($entries);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching entries: ' . $e->getMessage()]);
    }
}
?>