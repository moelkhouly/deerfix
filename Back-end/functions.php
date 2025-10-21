<?php
/*
==========================================================
Deerfix Website - Backend Functions
Created: October 2025
Description: All PHP functions for Deerfix bilingual website
Features: 
- Multilingual support (Arabic/English)
- Products management
- Blog system  
- Contact forms
- File uploads
- Email services
==========================================================
*/

// ==========================================================
//  Copyright Reserved Wael Wael Abo Hamza (Course Ecommerce)
// ==========================================================

// date_default_timezone_set('Africa/Cairo');

define("MB", 1048576);

// Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Send email using PHPMailer
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $body Email body
 * @param bool $isHTML Is HTML email
 * @param string $fromName Sender name
 * @return bool
 */




function sendMail($to, $subject, $body, $isHTML = false, $fromName = 'Deerfix NoReply') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.titan.email';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreply@deerfix.com';
        $mail->Password   = 'your-password-here';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        // Recipients
        $mail->setFrom('noreply@deerfix.com', $fromName);
        $mail->addAddress($to);

        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("sendMail() error: " . $mail->ErrorInfo);
        error_log("sendMail() exception: " . $e->getMessage());
        return false;
    }
}

/**
 * Simple email function using PHP mail() as fallback
 */
function sendSimpleMail($to, $subject, $body, $isHTML = false) {
    $headers = "From: Deerfix <noreply@deerfix.com>\r\n";
    $headers .= "Reply-To: contact@deerfix.com\r\n";
    $headers .= "Content-Type: text/" . ($isHTML ? "html" : "plain") . "; charset=UTF-8\r\n";
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Sanitize and filter request data
 * @param string $requestname POST parameter name
 * @return string
 */
function filterRequest($requestname) {
    return htmlspecialchars(strip_tags($_POST[$requestname]));
}

/**
 * Get all data from table with optional conditions
 * @param string $table Table name
 * @param string|null $where WHERE clause
 * @param array|null $values Parameters for WHERE
 * @param bool $json Return as JSON
 * @return mixed
 */
function getAllData($table, $where = null, $values = null, $json = true) {
    global $con;
    $data = array();
    
    if ($where == null) {
        $stmt = $con->prepare("SELECT * FROM $table");
    } else {
        $stmt = $con->prepare("SELECT * FROM $table WHERE $where");
    }
    
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count;
    } else {
        if ($count > 0) {
            return array("status" => "success", "data" => $data);
        } else {
            return array("status" => "failure");
        }
    }
}

/**
 * Get paginated data from table
 * @param string $table Table name
 * @param int $limit Number of records per page
 * @param int $offset Offset for pagination
 * @param string|null $where WHERE clause
 * @param array|null $values Parameters for WHERE
 * @param bool $json Return as JSON
 * @return mixed
 */
function getAllDataPagination($table, $limit = 5, $offset = 0, $where = null, $values = null, $json = true) {
    global $con;
    $data = array();
    
    if ($where == null) {
        $stmt = $con->prepare("SELECT * FROM $table LIMIT :limit OFFSET :offset");
    } else {
        $stmt = $con->prepare("SELECT * FROM $table WHERE $where LIMIT :limit OFFSET :offset");
    }
    
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute($values);
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();

    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count;
    } else {
        if ($count > 0) {
            return array("status" => "success", "data" => $data);
        } else {
            return array("status" => "failure");
        }
    }
}

/**
 * Get custom columns from table
 * @param string $table Table name
 * @param string $custom Custom SELECT columns
 * @param string|null $where WHERE clause
 * @param array|null $values Parameters for WHERE
 * @param bool $json Return as JSON
 * @return mixed
 */
function getAllCustomData($table, $custom = '*', $where = null, $values = null, $json = true) {
    global $con;
    $data = array();
    
    if ($where == null) {
        $stmt = $con->prepare("SELECT $custom FROM $table");
    } else {
        $stmt = $con->prepare("SELECT $custom FROM $table WHERE $where");
    }
    
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count;
    } else {
        if ($count > 0) {
            return array("status" => "success", "data" => $data);
        } else {
            return array("status" => "failure");
        }
    }
}

/**
 * Get single record from table
 * @param string $table Table name
 * @param string $where WHERE clause
 * @param array|null $values Parameters for WHERE
 * @param bool $json Return as JSON
 * @return mixed
 */
function getData($table, $where = null, $values = null, $json = true) {
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT * FROM $table WHERE $where");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    } else {
        return $count;
    }
}

/**
 * Insert data into table
 * @param string $table Table name
 * @param array $data Associative array of data
 * @param bool $json Return as JSON
 * @return int Number of affected rows
 */
function insertData($table, $data, $json = true) {
    global $con;
    
    foreach ($data as $field => $v) {
        $ins[] = ':' . $field;
    }
    
    $ins = implode(',', $ins);
    $fields = implode(',', array_keys($data));
    $sql = "INSERT INTO $table ($fields) VALUES ($ins)";

    $stmt = $con->prepare($sql);
    
    foreach ($data as $f => $v) {
        $stmt->bindValue(':' . $f, $v);
    }
    
    $stmt->execute();
    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    
    return $count;
}

/**
 * Update data in table with specific field
 * @param string $table Table name
 * @param array $data Data to update
 * @param string $where_field WHERE field name
 * @param mixed $where_value WHERE value
 * @param bool $json Return as JSON
 * @return int Number of affected rows
 */
function updateDataT($table, $data, $where_field, $where_value, $json = true) {
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = $val;
        $cols[] = "`$key` = ?";
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE `$where_field` = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute(array_merge($vals, [$where_value]));

    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    
    return $count;
}

/**
 * Update data in table with custom WHERE
 * @param string $table Table name
 * @param array $data Data to update
 * @param string $where WHERE clause
 * @param bool $json Return as JSON
 * @return int Number of affected rows
 */
function updateData($table, $data, $where, $json = true) {
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` = ?";
    }
    
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE $where";
    $stmt = $con->prepare($sql);
    $stmt->execute($vals);
    
    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    
    return $count;
}

/**
 * Delete data from table
 * @param string $table Table name
 * @param string $where WHERE clause
 * @param bool $json Return as JSON
 * @return int Number of affected rows
 */
function deleteData($table, $where, $json = true) {
    global $con;
    $stmt = $con->prepare("DELETE FROM $table WHERE $where");
    $stmt->execute();
    $count = $stmt->rowCount();
    
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success"));
        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    
    return $count;
}

/**
 * Get all file names from directory
 * @param string $dir Directory path
 */
function getFileNames($dir) {
    $filenames = [];
    
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filenames[] = $file;
            }
        }
    }

    echo json_encode(array("status" => "success", "data" => $filenames));
}

/**
 * Upload single image
 * @param string $dir Upload directory
 * @param string $imageRequest File input name
 * @param string $id File identifier
 * @return string|bool Filename or false on failure
 */
function imageUpload($dir, $imageRequest, $id) {
    if (isset($_FILES[$imageRequest])) {
        global $msgError;
        
        $imagename = $id . $_FILES[$imageRequest]['name'];
        $imagetmp = $_FILES[$imageRequest]['tmp_name'];
        $imagesize = $_FILES[$imageRequest]['size'];
        $allowExt = array("jpg", "png", "gif", "mp3", "pdf", "svg", "jpeg", "webp");
        $strToArray = explode(".", $imagename);
        $ext = end($strToArray);
        $ext = strtolower($ext);

        if (!empty($imagename) && !in_array($ext, $allowExt)) {
            $msgError = "EXT";
        }
        
        if ($imagesize > 2 * MB) {
            $msgError = "size";
        }
        
        if (empty($msgError)) {
            move_uploaded_file($imagetmp, $dir . "/" . $imagename);
            return $imagename;
        } else {
            return "fail";
        }
    } else {
        return "empty";
    }
}

/**
 * Upload multiple images
 * @param string $dir Upload directory
 * @param string $inputName File input name
 * @return array Array of uploaded filenames
 */
function multiImageUpload($dir, $inputName) {
    $uploadedImages = [];

    if (isset($_FILES[$inputName])) {
        $files = $_FILES[$inputName];

        for ($i = 0; $i < count($files['name']); $i++) {
            $imagename = rand(1000, 10000) . $files['name'][$i];
            $imagetmp = $files['tmp_name'][$i];
            $imagesize = $files['size'][$i];
            $allowExt = array("jpg", "png", "gif", "svg", "PNG", "jpeg", "webp");

            $ext = strtolower(pathinfo($imagename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowExt)) {
                continue;
            }
            
            if ($imagesize > 5 * MB) {
                continue;
            }

            if (move_uploaded_file($imagetmp, $dir . "/" . $imagename)) {
                $uploadedImages[] = $imagename;
            }
        }
    }

    return $uploadedImages;
}

/**
 * Delete file from directory
 * @param string $dir Directory path
 * @param string $imagename Filename to delete
 */
function deleteFile($dir, $imagename) {
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

/**
 * Basic authentication check
 */
function checkAuthenticate() {
    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
        if ($_SERVER['PHP_AUTH_USER'] != "wael" || $_SERVER['PHP_AUTH_PW'] != "wael12345") {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }
}

/**
 * Generate random string
 * @param int $length Length of random string
 * @return string Random string
 */
function generateRandomString($length) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}

/**
 * Print failure response
 * @param string $message Failure message
 */
function printFailure($message = "none") {
    echo json_encode(array("status" => "failure", "message" => $message));
}

/**
 * Print success response
 * @param string $message Success message
 */
function printSuccess($message = "none") {
    echo json_encode(array("status" => "success", "message" => $message));
}

/**
 * Print result based on count
 * @param int $count Number of affected rows
 */
function result($count) {
    if ($count > 0) {
        printSuccess();
    } else {
        printFailure();
    }
}

/**
 * Send GCM notification
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string $topic Notification topic
 * @param string $pageid Page ID
 * @param string $pagename Page name
 * @return mixed
 */
function sendGCM($title, $message, $topic, $pageid, $pagename) {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        "to" => '/topics/' . $topic,
        'priority' => 'high',
        'content_available' => true,
        'notification' => array(
            "body" => $message,
            "title" => $title,
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default"
        ),
        'data' => array(
            "pageid" => $pageid,
            "pagename" => $pagename
        )
    );

    $fields = json_encode($fields);
    $headers = array(
        'Authorization: key=' . "AAAAI48otjU:APA91bHYtxMfPbwNB2BQP-Y9JYIaW5SnMNpltAh53r5tIEcAYv-jI-T7CAjacoR4vyWqsCSDRrkSyqhaLIIzvWc02uKRjKZmHnxJwhJXAvTAIRvgQQbD0mcPdOKQTSHfE6MRCkmcsl4x",
        'Content-Type: application/json'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result;
}

/**
 * Send simple email
 * @param string $to Recipient email
 * @param string $title Email subject
 * @param string $body Email body
 */
function sendEmail($to, $title, $body) {
    $header = "From: support@mawanak.com";
    mail($to, $title, $body, $header);
}

/**
 * Insert notification
 * @param string $title Notification title
 * @param string $body Notification body
 * @param string $userid User ID
 * @param string $topic Topic
 * @param string $pageid Page ID
 * @param string $pagename Page name
 * @return int Number of affected rows
 */
function insertNotify($title, $body, $userid, $topic, $pageid, $pagename) {
    global $con;
    
    $stmt = $con->prepare("INSERT INTO `notification`(`notification_title`, `notification_body`, `notification_userid`) VALUES (?,?,?)");
    $stmt->execute(array($title, $body, $userid));
    sendGCM($title, $body, $topic, $pageid, $pagename);
    
    $count = $stmt->rowCount();
    return $count;
}
















// ==========================================================
// Deerfix Custom Functions
// ==========================================================

/**
 * Get all products with language support
 * @param string $lang Language ('en' or 'ar')
 * @param string|null $category_slug Category slug filter
 * @param bool|null $featured Featured products filter
 * @param bool|null $new New products filter
 * @return array
 */
function getProducts($lang = 'en', $category_slug = null, $featured = null, $new = null) {
    global $con;
    
    $query = "SELECT p.*, 
                     c.name_$lang as category_name,
                     c.slug_$lang as category_slug
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.is_active = 1";
    
    $params = [];
    
    if ($category_slug) {
        $query .= " AND c.slug_$lang = ?";
        $params[] = $category_slug;
    }
    
    if ($featured !== null) {
        $query .= " AND p.is_featured = ?";
        $params[] = $featured;
    }
    
    if ($new !== null) {
        $query .= " AND p.is_new = ?";
        $params[] = $new;
    }
    
    $query .= " ORDER BY p.created_at DESC";
    
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single product by slug with language support
 * @param string $slug Product slug
 * @param string $lang Language ('en' or 'ar')
 * @return array|null
 */
function getProductBySlug($slug, $lang = 'en') {
    global $con;
    
    $stmt = $con->prepare("SELECT p.*, 
                                  c.name_$lang as category_name,
                                  c.slug_$lang as category_slug
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.slug_$lang = ? AND p.is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get all categories with language support
 * @param string $lang Language ('en' or 'ar')
 * @param int|null $parent_id Parent category ID
 * @return array
 */
function getCategories($lang = 'en', $parent_id = null) {
    global $con;
    
    $query = "SELECT * FROM categories WHERE is_active = 1";
    $params = [];
    
    if ($parent_id !== null) {
        $query .= " AND parent_id = ?";
        $params[] = $parent_id;
    } else {
        $query .= " AND parent_id IS NULL";
    }
    
    $query .= " ORDER BY name_$lang";
    
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get blog posts with language support
 * @param string $lang Language ('en' or 'ar')
 * @param bool|null $featured Featured posts filter
 * @param int|null $limit Number of posts to return
 * @return array
 */
function getBlogPosts($lang = 'en', $featured = null, $limit = null) {
    global $con;
    
    $query = "SELECT * FROM blogs WHERE is_published = 1";
    $params = [];
    
    if ($featured !== null) {
        $query .= " AND is_featured = ?";
        $params[] = $featured;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $query .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single blog post by slug with language support
 * @param string $slug Blog post slug
 * @param string $lang Language ('en' or 'ar')
 * @return array|null
 */
function getBlogPostBySlug($slug, $lang = 'en') {
    global $con;
    
    $stmt = $con->prepare("SELECT * FROM blogs WHERE slug_$lang = ? AND is_published = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Insert contact message
 * @param string $name Contact name
 * @param string $email Contact email
 * @param string $message Contact message
 * @param string|null $phone Contact phone
 * @param string|null $company Contact company
 * @param string|null $subject Contact subject
 * @return int Number of affected rows
 */
function insertContactMessage($name, $email, $message, $phone = null, $company = null, $subject = null) {
    $data = [
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'phone' => $phone,
        'company' => $company,
        'subject' => $subject
    ];
    
    return insertData('contacts', $data, false);
}

/**
 * Increment product views count
 * @param int $product_id Product ID
 * @return bool
 */
function incrementProductViews($product_id) {
    global $con;
    
    $stmt = $con->prepare("UPDATE products SET views_count = views_count + 1 WHERE id = ?");
    return $stmt->execute([$product_id]);
}

/**
 * Increment blog views count
 * @param int $blog_id Blog ID
 * @return bool
 */
function incrementBlogViews($blog_id) {
    global $con;
    
    $stmt = $con->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
    return $stmt->execute([$blog_id]);
}

/**
 * Search products with language support
 * @param string $query Search query
 * @param string $lang Language ('en' or 'ar')
 * @return array
 */
function searchProducts($query, $lang = 'en') {
    global $con;
    
    $stmt = $con->prepare("SELECT * FROM products 
                          WHERE (name_$lang LIKE ? OR description_$lang LIKE ?) 
                          AND is_active = 1 
                          ORDER BY views_count DESC");
    
    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get featured products
 * @param string $lang Language ('en' or 'ar')
 * @param int $limit Number of products
 * @return array
 */
function getFeaturedProducts($lang = 'en', $limit = 6) {
    global $con;
    
    $stmt = $con->prepare("SELECT p.*, c.name_$lang as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.is_featured = 1 AND p.is_active = 1 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get new products
 * @param string $lang Language ('en' or 'ar')
 * @param int $limit Number of products
 * @return array
 */
function getNewProducts($lang = 'en', $limit = 6) {
    global $con;
    
    $stmt = $con->prepare("SELECT p.*, c.name_$lang as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.is_new = 1 AND p.is_active = 1 
                          ORDER BY p.created_at DESC 
                          LIMIT ?");
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Calculate product quantity for the calculator
 * @param float $width Width dimension
 * @param float $depth Depth dimension
 * @param float $length Length dimension
 * @param float $packaging_volume Packaging volume
 * @return float
 */
function calculateProductQuantity($width, $depth, $length, $packaging_volume) {
    if ($packaging_volume <= 0) {
        return 0;
    }
    
    $total_volume = $width * $depth * $length;
    $quantity = ceil($total_volume / $packaging_volume);
    
    return $quantity;
}

/**
 * Subscribe to newsletter
 * @param string $email Subscriber email
 * @param string|null $name Subscriber name
 * @return string|int
 */
function subscribeToNewsletter($email, $name = null) {
    global $con;
    
    // Check if email already exists
    $stmt = $con->prepare("SELECT id FROM subscribers WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        return "exists";
    }
    
    $data = [
        'email' => $email,
        'name' => $name
    ];
    
    return insertData('subscribers', $data, false);
}

/**
 * Get all blogs with language support
 * @param string $lang Language ('en' or 'ar')
 * @param string|null $category_slug Category slug filter
 * @param int|null $featured Featured filter (1 or 0)
 * @param int|null $new New filter (1 or 0)
 * @return array
 */
function getBlogs($lang = 'en', $category_slug = null, $featured = null, $new = null) {
    global $con;
    
    $query = "SELECT b.*, 
                     c.name_$lang as category_name,
                     c.slug_$lang as category_slug
              FROM blogs b 
              LEFT JOIN categories c ON b.category_id = c.id 
              WHERE b.is_active = 1";
    
    $params = [];
    
    if ($category_slug) {
        $query .= " AND c.slug_$lang = ?";
        $params[] = $category_slug;
    }
    
    if ($featured !== null) {
        $query .= " AND b.is_featured = ?";
        $params[] = $featured;
    }
    
    if ($new !== null) {
        $query .= " AND b.is_new = ?";
        $params[] = $new;
    }
    
    $query .= " ORDER BY b.created_at DESC";
    
    $stmt = $con->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get single blog by slug with language support
 * @param string $slug Blog slug
 * @param string $lang Language ('en' or 'ar')
 * @return array|null
 */
function getBlogBySlug($slug, $lang = 'en') {
    global $con;
    
    $stmt = $con->prepare("SELECT b.*, 
                                  c.name_$lang as category_name,
                                  c.slug_$lang as category_slug
                           FROM blogs b 
                           LEFT JOIN categories c ON b.category_id = c.id 
                           WHERE b.slug_$lang = ? AND b.is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

?>