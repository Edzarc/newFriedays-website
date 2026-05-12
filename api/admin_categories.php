<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            // Get all categories
            $categories = getAllCategories();
            echo json_encode(['success' => true, 'categories' => $categories]);
            break;

        case 'POST':
            // Add new category
            $name = trim($data['name'] ?? '');

            if (empty($name)) {
                echo json_encode(['success' => false, 'message' => 'Category name is required']);
                exit();
            }

            if (strlen($name) > 255) {
                echo json_encode(['success' => false, 'message' => 'Category name is too long']);
                exit();
            }

            try {
                $categoryId = addCategory($name);
                echo json_encode(['success' => true, 'message' => 'Category added successfully', 'category_id' => $categoryId, 'name' => $name]);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate') !== false) {
                    echo json_encode(['success' => false, 'message' => 'Category already exists']);
                } else {
                    throw $e;
                }
            }
            break;

        case 'DELETE':
            // Delete category
            $categoryId = intval($_GET['id'] ?? 0);

            if (!$categoryId) {
                echo json_encode(['success' => false, 'message' => 'Invalid category ID']);
                exit();
            }

            $success = deleteCategory($categoryId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Category is in use or not found']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
