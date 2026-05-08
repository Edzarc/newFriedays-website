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
        case 'POST':
            // Add new product
            $name = trim($data['name'] ?? '');
            $category = $data['category'] ?? '';
            $price = floatval($data['price'] ?? 0);
            $description = trim($data['description'] ?? '');
            $imageUrl = trim($data['image_url'] ?? '');

            if (empty($name) || empty($category) || $price <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid product data']);
                exit();
            }

            $productId = addProduct($name, $category, $price, $description, $imageUrl ?: null);
            echo json_encode(['success' => true, 'message' => 'Product added successfully', 'product_id' => $productId]);
            break;

        case 'PUT':
            // Update product or toggle availability
            $productId = intval($data['product_id'] ?? 0);
            
            // Check if this is an availability toggle
            if (isset($data['toggle_availability'])) {
                $available = intval($data['is_available'] ?? 0);
                if (!$productId) {
                    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                    exit();
                }
                $success = toggleProductAvailability($productId, $available);
                if ($success) {
                    echo json_encode(['success' => true, 'message' => $available ? 'Product marked as available' : 'Product marked as unavailable']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Product not found or update failed']);
                }
            } else {
                // Regular product update
                $name = trim($data['name'] ?? '');
                $category = $data['category'] ?? '';
                $price = floatval($data['price'] ?? 0);
                $description = trim($data['description'] ?? '');
                $imageUrl = trim($data['image_url'] ?? '');

                if (!$productId || empty($name) || empty($category) || $price <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Invalid product data']);
                    exit();
                }

                $success = updateProduct($productId, $name, $category, $price, $description, $imageUrl ?: null);
                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Product not found or update failed']);
                }
            }
            break;

        case 'DELETE':
            // Delete product
            $productId = intval($_GET['id'] ?? 0);

            if (!$productId) {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                exit();
            }

            $success = deleteProduct($productId);
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Product not found or delete failed']);
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