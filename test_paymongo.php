<?php
require_once 'config/db.php';

echo "Testing PayMongo GCash Source Creation...\n\n";

// Test with a sample amount
$testAmount = 100.00; // ₱100.00
$testOrderId = 999;

echo "Test Parameters:\n";
echo "- Amount: ₱" . $testAmount . "\n";
echo "- Order ID: " . $testOrderId . "\n";
echo "- API URL: " . PAYMONGO_BASE_URL . "/sources\n";
echo "- Secret Key: " . substr(PAYMONGO_SECRET_KEY, 0, 10) . "...\n\n";

$result = createPayMongoGCashSource($testAmount, "Test Order", $testOrderId);

echo "Result:\n";
if (isset($result['error'])) {
    echo "❌ ERROR: " . $result['message'] . "\n";
    if (isset($result['http_code'])) {
        echo "HTTP Code: " . $result['http_code'] . "\n";
    }
} else {
    echo "✅ SUCCESS!\n";
    echo "Source ID: " . $result['id'] . "\n";
    echo "Checkout URL: " . $result['attributes']['redirect']['checkout_url'] . "\n";
    echo "Status: " . $result['attributes']['status'] . "\n";
}

function createPayMongoGCashSource($amount, $description, $orderId) {
    // Validate amount (minimum 1 PHP = 100 centavos)
    if ($amount < 1) {
        return [
            'error' => true,
            'message' => 'Amount must be at least ₱1.00'
        ];
    }

    $url = PAYMONGO_BASE_URL . '/sources';

    // Construct proper base URL
    if (php_sapi_name() === 'cli') {
        // For CLI/testing, use localhost
        $baseUrl = 'http://localhost/newFriedays-website';
    } else {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/newFriedays-website/index.php');
        $baseUrl = $protocol . "://" . $host . $scriptDir;
        $baseUrl = rtrim($baseUrl, '/');
    }

    echo "Constructed Base URL: " . $baseUrl . "\n";

    $data = [
        'data' => [
            'attributes' => [
                'amount' => intval($amount * 100), // Convert to centavos
                'currency' => 'PHP',
                'type' => 'gcash',
                'redirect' => [
                    'success' => $baseUrl . '/index.php?page=payment_success&order_id=' . $orderId,
                    'failed' => $baseUrl . '/index.php?page=payment_failed&order_id=' . $orderId
                ],
                'billing' => [
                    'name' => 'Friedays Bocaue',
                    'email' => 'orders@friedaysbocaue.com'
                ]
            ]
        ]
    ];

    echo "Request Data:\n" . json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode(PAYMONGO_SECRET_KEY . ':')
    ]);

    // Add timeout and SSL verification
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    echo "Making API request...\n";
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    echo "HTTP Code: " . $httpCode . "\n";

    if ($curlError) {
        return [
            'error' => true,
            'message' => 'Network error: ' . $curlError
        ];
    }

    echo "Response: " . $response . "\n\n";

    if ($httpCode === 200 || $httpCode === 201) {
        $result = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => true,
                'message' => 'Invalid JSON response from PayMongo'
            ];
        }
        return $result['data'];
    } else {
        $errorData = json_decode($response, true);
        $errorMessage = isset($errorData['errors'][0]['detail']) ? $errorData['errors'][0]['detail'] : $response;

        return [
            'error' => true,
            'http_code' => $httpCode,
            'message' => 'PayMongo API Error: ' . $errorMessage
        ];
    }
}
?>