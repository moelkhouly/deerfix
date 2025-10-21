<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");
header("Access-Control-Allow-Methods: GET, OPTIONS");

require_once '../connect.php';
require_once '../functions.php';

/*
==========================================================
Deerfix Products API - Get All Products
Endpoint: GET /api/products/get_all.php
Parameters:
- lang (optional): en | ar (default: en)
- category (optional): category slug
- featured (optional): 1 | 0
- new (optional): 1 | 0
==========================================================
*/

try {
    // Get parameters
    $lang = isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ar']) ? $_GET['lang'] : 'en';
    $category_slug = isset($_GET['category']) ? $_GET['category'] : null;
    $featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;
    $new = isset($_GET['new']) ? (int)$_GET['new'] : null;

    // Get products using Deerfix function
    $products = getProducts($lang, $category_slug, $featured, $new);

    if (!empty($products)) {
        // Format response
        $response = [
            'status' => 'success',
            'data' => $products,
            'count' => count($products),
            'lang' => $lang
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'success',
            'data' => [],
            'count' => 0,
            'message' => 'No products found',
            'lang' => $lang
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>