<?php

include "../connect.php"; // This should include vendor/sendMail.php too


$name = filterRequest("name");
$email = filterRequest("email");
$phone = filterRequest("phone");
$message = filterRequest("message");
$userSubject = filterRequest("subject");

try {
    // Log incoming request for debugging (raw body and parsed POST)
    $raw = file_get_contents('php://input');
    error_log("[contact.php] raw input: " . $raw);
    error_log("[contact.php] \\$_POST: " . json_encode($_POST));
    $myEmail = "supporttest@merkwave.com";

    // Construct the email body; put phone near the top and include the user's subject
    $body = <<<EOT
تم استلام رسالة جديدة من نموذج التواصل في الموقع:

الهاتف: $phone
الاسم: $name
البريد الإلكتروني: $email
الموضوع: $userSubject
الرسالة:
$message
EOT;

    // Use the user-provided subject when possible, fall back to a default Arabic subject
    $subject = trim($userSubject) !== '' ? "نموذج تواصل جديد: " . $userSubject : "نموذج تواصل جديد من الموقع";

    // Use global sendMail() with custom from name
    $sent = sendMail($myEmail, $subject, $body, false, "MerkWave Contact");

    if ($sent) {
        header('Content-Type: application/json; charset=utf-8');
        $out = ['status' => 'success', 'message' => 'تم إرسال الرسالة بنجاح!'];
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        header('Content-Type: application/json; charset=utf-8', true, 500);
        $out = ['status' => 'failure', 'message' => 'فشل في إرسال الرسالة. حاول مرة أخرى لاحقاً.'];
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }

} catch (\Throwable $th) {
    header('Content-Type: application/json; charset=utf-8', true, 500);
    $out = ['status' => 'failure', 'message' => 'حدث خطأ: ' . $th->getMessage()];
    error_log('[contact.php] exception: ' . $th->getMessage());
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}
