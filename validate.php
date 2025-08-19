<!-- <?php
// Show all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// ---------------- Load configuration ---------------- //
require __DIR__ . '/config.php';

// ---------------- Load PHPMailer ---------------- //
require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ---------------- Google reCAPTCHA ---------------- //
$recaptchaSecret = "6LflSqsrAAAAAEEA1Gd62g-5QlOdsTZ4Qo8xJdNg";
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptchaResponse)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please complete the reCAPTCHA.'
    ]);
    exit;
}

$verifyResponse = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}"
);
$responseData = json_decode($verifyResponse);

if (!$responseData->success) {
    echo json_encode([
        'success' => false,
        'message' => 'reCAPTCHA verification failed. Please try again.'
    ]);
    exit;
}

// ---------------- Sanitize inputs ---------------- //
$name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// ---------------- Validate required fields ---------------- //
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields.'
    ]);
    exit;
}

// ---------------- Validate email format ---------------- //
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

// ---------------- Prepare email content ---------------- //
$email_subject = $subject ?: "New message from your portfolio";

$email_content = "<html><body>";
$email_content .= "<h2>New Message from Portfolio Contact Form</h2>";
$email_content .= "<p><strong>Name:</strong> $name</p>";
$email_content .= "<p><strong>Email:</strong> $email</p>";
if ($subject) {
    $email_content .= "<p><strong>Subject:</strong> $subject</p>";
}
$email_content .= "<p><strong>Message:</strong></p>";
$email_content .= "<p>" . nl2br($message) . "</p>";
$email_content .= "</body></html>";

// ---------------- Send Email via PHPMailer ---------------- //
try {
    $mail = new PHPMailer(true);

    // SMTP settings from config.php
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    // Sender & recipient
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress(CONTACT_TO_EMAIL, CONTACT_TO_NAME);
    $mail->addReplyTo($email, $name);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = $email_subject;
    $mail->Body    = $email_content;

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => '✅ Message sent successfully via PHPMailer (SMTP).'
    ]);
    exit;

} catch (Exception $e) {
    // ---------------- Fallback: PHP mail() ---------------- //
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail(CONTACT_TO_EMAIL, $email_subject, $email_content, $headers)) {
        echo json_encode([
            'success' => true,
            'message' => '⚠️ PHPMailer failed, but message sent via PHP mail().'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '❌ Sending failed (both PHPMailer and mail()).'
        ]);
    }
}
?> -->
