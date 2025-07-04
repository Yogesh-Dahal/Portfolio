<?php
header('Content-Type: application/json');

// Validate and sanitize input
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit;
}

// Check for empty fields
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode([
        'success' => false,
        'message' => 'Please fill in all required fields.'
    ]);
    exit;
}

// Email configuration
$to = 'dahalyogesh9842@gmail.com';
$headers = "From: $name <$email>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Email subject
$email_subject = $subject ?: "New message from your portfolio";

// Email content
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

// Send email
if (mail($to, $email_subject, $email_content, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Oops! Something went wrong and we couldn\'t send your message.'
    ]);
}
?>