<?php
header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and assign form inputs to variables
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message_content = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit;
    }

    // Define email subject and message for recipient
    $recipient_subject = "Contact Form Submission";
    $recipient_message = "<html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            color: #333;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                        }
                        .container {
                            width: 100%;
                            max-width: 600px;
                            margin: 20px auto;
                            padding: 20px;
                            background-color: #fff;
                            border-radius: 8px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .header img {
                            max-width: 150px;
                        }
                        .content {
                            font-size: 16px;
                            line-height: 1.5;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 20px;
                            font-size: 12px;
                            color: #777;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <img src='https://bnmretail.com/assets/Logo.png' alt='BNM Retail Logo'>
                        </div>
                        <div class='content'>
                            <p><strong>Dear BNM Retail Team,</strong></p>
                            <p>You have received a new message from the contact form:</p>
                            <p><strong>Name:</strong> " . $name . "</p>
                            <p><strong>Email:</strong> " . $email . "</p>
                            <p><strong>Message:</strong></p>
                            <p>" . nl2br($message_content) . "</p>
                            <p>Best regards,</p>
                            <p>The Contact Form</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " BNM Retail. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

    // Email headers for recipient
    $headers = "From: no-reply@waviatorecom.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";

    // Email recipient
    $recipient = "hr@bnmretail.com";
    $bcc_addresses = ['hrd@bnmretail.com', 'shitaanshus27@waviator.in'];
    $bcc_header = "Bcc: " . implode(", ", $bcc_addresses) . "\r\n";
    $headers .= $bcc_header;

    // Send the email to the recipient
    $send_to_recipient = mail($recipient, $recipient_subject, $recipient_message, $headers);

    // Define email subject and message for the form filler
    $filler_subject = "Thank you for your message";
    $filler_message = "<html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            color: #333;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                        }
                        .container {
                            width: 100%;
                            max-width: 600px;
                            margin: 20px auto;
                            padding: 20px;
                            background-color: #fff;
                            border-radius: 8px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .header img {
                            max-width: 150px;
                        }
                        .content {
                            font-size: 16px;
                            line-height: 1.5;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 20px;
                            font-size: 12px;
                            color: #777;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <img src='https://bnm.waviatorecom.com/assets/Logo.png' alt='BNM Retail Logo'>
                        </div>
                        <div class='content'>
                            <p><strong>Dear " . $name . ",</strong></p>
                            <p>Thank you for contacting BNM Retail. We have received your message and will get back to you soon.</p>
                            <p>Here are the details you provided:</p>
                            <p><strong>Name:</strong> " . $name . "</p>
                            <p><strong>Email:</strong> " . $email . "</p>
                            <p><strong>Message:</strong></p>
                            <p>" . nl2br($message_content) . "</p>
                            <p>Best regards,</p>
                            <p>BNM Retail Team</p>
                        </div>
                        <div class='footer'>
                            <p>&copy; " . date('Y') . " BNM Retail. All rights reserved.</p>
                        </div>
                    </div>
                </body>
                </html>";

    // Email headers for the form filler
    $filler_headers = "From: no-reply@waviatorecom.com\r\n";
    $filler_headers .= "MIME-Version: 1.0\r\n";
    $filler_headers .= "Content-type: text/html; charset=UTF-8\r\n";

    // Send the email to the form filler
    $send_to_filler = mail($email, $filler_subject, $filler_message, $filler_headers);

    // Determine overall success
    if ($send_to_recipient && $send_to_filler) {
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send your message. Please try again later.']);
    }
}
?>
