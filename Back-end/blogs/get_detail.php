
<?php
include_once "../connect.php";

// Add CORS headers to allow requests from localhost
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}



try {


$slug = $_GET['slug'];



getAllCustomData("blogs","*", "slug = '$slug' ");


} catch (\Throwable $th) {

printFailure("An error occurred: " . $th->getMessage() . " on line " . $th->getLine());
 
}


