<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize the form data
    $fullName = htmlspecialchars($_POST['fullName']);
    $experience = htmlspecialchars($_POST['experience']);
    $address = htmlspecialchars($_POST['address']);
    $postcode = htmlspecialchars($_POST['postcode']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $position = htmlspecialchars($_POST['position']);
    $whyHire = htmlspecialchars($_POST['whyHire']);

    // Handle file upload (resume)
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == UPLOAD_ERR_OK) {
        $resume = $_FILES['fileUpload'];
        $file_name = basename($resume['name']);
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

        // Check if file is PDF
        if ($file_type != 'pdf') {
            echo json_encode(['success' => false, 'message' => 'Please upload a valid PDF file.']);
            exit;
        }

        // Set a directory to store uploaded files
        $upload_dir = 'uploads/';
        $upload_file_path = $upload_dir . $file_name;

        // Check if the directory exists and is writable
        if (!is_dir($upload_dir) || !is_writable($upload_dir)) {
            echo json_encode(['success' => false, 'message' => 'Upload directory does not exist or is not writable.']);
            exit;
        }

        // Move uploaded file
        if (!move_uploaded_file($resume['tmp_name'], $upload_file_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload the resume.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or there was an upload error.']);
        exit;
    }

    // Email details
    $to = "hrd@bnmretail.com";
    $subject = "New Job Application from $fullName";
    $message = "
    <html>
    <body>
        <h2>New Job Application</h2>
        <p><strong>Full Name:</strong> $fullName</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Position Applied For:</strong> $position</p>
        <p><strong>Experience:</strong> $experience years</p>
        <p><strong>Address:</strong> $address</p>
        <p><strong>Postcode:</strong> $postcode</p>
        <p><strong>Why Hire Me:</strong> $whyHire</p>
        <p><strong>Resume:</strong> <a href='https://bnmretail.com/$upload_file_path'>Download Resume</a></p>
    </body>
    </html>
    ";

    // Email headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: $email" . "\r\n";

    // Send the email to HR
    if (mail($to, $subject, $message, $headers)) {
        // Send confirmation email to the applicant
        $user_subject = "Thank you for applying to Baby and Mom Pvt Ltd";
        $user_message = "
        <html>
        <body>
            <h2>Dear $fullName,</h2>
            <p>Thank you for applying for the $position position. We have received your application and will review it shortly.</p>
        </body>
        </html>
        ";

        $user_headers = "MIME-Version: 1.0" . "\r\n";
        $user_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $user_headers .= "From: no-reply@waviatorecom.com" . "\r\n";

        mail($email, $user_subject, $user_message, $user_headers);

        // Respond with success in JSON
        echo json_encode(['success' => true, 'message' => 'Application submitted successfully.']);
    } else {
        // Respond with failure in JSON
        echo json_encode(['success' => false, 'message' => 'Failed to send application.']);
    }
}
?>
