<?php

// ==========================================================
//  Copyright Reserved Wael Wael Abo Hamza (Course Ecommerce)
// ==========================================================

// date_default_timezone_set('Africa/Cairo');

define("MB", 1048576);







require __DIR__ . '/vendor/autoload.php';



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to, $subject, $body, $isHTML = false, $fromName = 'MerkWave NoReply') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.titan.email';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'noreplytest@merkwave.com';
        $mail->Password   = '123456789mw%';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('noreplytest@merkwave.com', $fromName);
        $mail->addAddress($to);

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








function filterRequest($requestname)
{
    return  htmlspecialchars(strip_tags($_POST[$requestname]));
}

function getAllData($table, $where = null, $values = null,$json = true)
{
    global $con;
    $data = array();
    if ($where==null) {
        $stmt = $con->prepare("SELECT  * FROM $table ");
    } else {
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    }
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count; 
    }else {  
        if ($count >0 ) { 
            return (array("status" => "success" , "data" =>$data));    ;
        } else {
            return (array("status" => "failure"));
        }
    }

    
}

function getAllDataPagination($table, $limit = 5, $offset = 0, $where = null, $values = null, $json = true)
{
    global $con;
    $data = array();
    if ($where == null) {
        $stmt = $con->prepare("SELECT * FROM $table LIMIT :limit OFFSET :offset");
    } else {
        $stmt = $con->prepare("SELECT * FROM $table WHERE $where LIMIT :limit OFFSET :offset");
    }
    
    // Bind parameters for pagination
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
            return (array("status" => "success", "data" => $data));
        } else {
            return (array("status" => "failure"));
        }
    }
}



function getAllCustomData($table, $custom = '*',$where = null, $values = null,$json = true)
{
    global $con;
    $data = array();
    if ($where==null) {
        $stmt = $con->prepare("SELECT $custom  FROM $table ");
    } else {
    $stmt = $con->prepare("SELECT $custom  FROM $table WHERE   $where ");
    }
    $stmt->execute($values);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
        if ($count > 0) {
            echo json_encode(array("status" => "success", "data" => $data));
        } else {
            echo json_encode(array("status" => "failure"));
        }
        return $count; 
    }else {  
        if ($count >0 ) { 
            return (array("status" => "success" , "data" =>$data));    ;
        } else {
            return (array("status" => "failure"));
        }
    }

    
}






function getData($table, $where = null, $values = null,$json = true)
{
    global $con;
    $data = array();
    $stmt = $con->prepare("SELECT  * FROM $table WHERE   $where ");
    $stmt->execute($values);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count  = $stmt->rowCount();
    if ($json == true) {
    if ($count > 0) {
        echo json_encode(array("status" => "success", "data" => $data));
    } else {
        echo json_encode(array("status" => "failure"));
    }}else {
        return $count;
    }
    
}

 

function insertData($table, $data, $json = true)
{
    global $con;
    foreach ($data as $field => $v)
        $ins[] = ':' . $field;
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
            // echo "اى كلام";

        } else {
            echo json_encode(array("status" => "failure"));
        }
    }
    return $count;
}

function updateDataT($table, $data, $where_field, $where_value, $json = true)
{
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = $val; // No need for double quotes here
        $cols[] = "`$key` =  ? ";
    }
    $sql = "UPDATE $table SET " . implode(', ', $cols) . " WHERE `$where_field` = ?";

    $stmt = $con->prepare($sql);
    $stmt->execute(array_merge($vals, [$where_value])); // Bind the values

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
function updateData($table, $data, $where, $json = true)
{
    global $con;
    $cols = array();
    $vals = array();

    foreach ($data as $key => $val) {
        $vals[] = "$val";
        $cols[] = "`$key` =  ? ";
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


function deleteData($table, $where, $json = true)
{
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



function getFileNames($dir)
{
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


function imageUpload($dir,$imageRequest,$id)
{
    if (isset($_FILES[$imageRequest])) {
        # code...
    
    global $msgError;
    // $imagename  = rand(1000, 10000) . $_FILES[$imageRequest]['name'];
    $imagename  = $id . $_FILES[$imageRequest]['name'];
    // $imagename  = $_FILES[$imageRequest]['name'];
    $imagetmp   = $_FILES[$imageRequest]['tmp_name'];
    $imagesize  = $_FILES[$imageRequest]['size'];
    $allowExt   = array("jpg", "png", "gif", "mp3", "pdf", "svg");
    $strToArray = explode(".", $imagename);
    $ext        = end($strToArray);
    $ext        = strtolower($ext);

    if (!empty($imagename) && !in_array($ext, $allowExt)) {
        $msgError = "EXT";
    }
    if ($imagesize > 2 * MB) {
        $msgError = "size";
    }
    if (empty($msgError)) {
        move_uploaded_file($imagetmp,  $dir."/". $imagename);
        return $imagename;
    } else {
        return "fail";
    }
}else {
    return "empty";
}
}

function multiImageUpload($dir, $inputName) {
    $uploadedImages = [];

    if (isset($_FILES[$inputName])) {
        $files = $_FILES[$inputName];

        for ($i = 0; $i < count($files['name']); $i++) {
            $imagename = rand(1000, 10000) . $files['name'][$i];
            $imagetmp = $files['tmp_name'][$i];
            $imagesize = $files['size'][$i];
            $allowExt = array("jpg", "png", "gif", "svg","PNG");

            $ext = strtolower(pathinfo($imagename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowExt)) {
                continue; // skip invalid ext
            }
            if ($imagesize > 5 * MB) {
                continue; // skip large files
            }

            if (move_uploaded_file($imagetmp, $dir . "/" . $imagename)) {
                $uploadedImages[] = $imagename;
            }
        }
    }

    return $uploadedImages;
}




function deleteFile($dir, $imagename)
{
    if (file_exists($dir . "/" . $imagename)) {
        unlink($dir . "/" . $imagename);
    }
}

function checkAuthenticate()
{
    if (isset($_SERVER['PHP_AUTH_USER'])  && isset($_SERVER['PHP_AUTH_PW'])) {
        if ($_SERVER['PHP_AUTH_USER'] != "wael" ||  $_SERVER['PHP_AUTH_PW'] != "wael12345") {
            header('WWW-Authenticate: Basic realm="My Realm"');
            header('HTTP/1.0 401 Unauthorized');
            echo 'Page Not Found';
            exit;
        }
    } else {
        exit;
    }

    // End 
}


function generateRandomString($length) {
    // Define the characters to use
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        // Pick a random character from the string
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
}




function   printFailure($message = "none") 
{
    echo     json_encode(array("status" => "failure" , "message" => $message));
}

function   printSuccess($message = "none") 
{
    echo     json_encode(array("status" => "success" , "message" => $message));
}

function result($count) {
if ($count > 0) {
    printSuccess();
}else {
    printFailure();
}
}





function sendGCM($title, $message, $topic, $pageid, $pagename)
{


    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array(
        "to" => '/topics/' . $topic,
        'priority' => 'high',
        'content_available' => true,

        'notification' => array(
            "body" =>  $message,
            "title" =>  $title,
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
    return $result;
    curl_close($ch);
}


function sendEmail($to , $title , $body){
// $header = "From : support@sphinxsoft.com" . "\n" . "CC: Test@gmail.com"  ; 
 $header = "From : support@mawanak.com"   ; 


mail($to , $title , $body , $header) ; 
// echo "Success" ; 
}

function insertNotify($title, $body , $userid, $topic,$pageid,$pagename){
global $con;
$stmt = $con->prepare("INSERT INTO `notification`( `notification_title`, `notification_body`, `notification_userid`) VALUES (?,?,?)");
$stmt->execute(array($title, $body , $userid));
sendGCM($title,$body,$topic,$pageid,$pagename);
$count = $stmt->rowCount();
return $count;

}