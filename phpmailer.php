<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);
     $date = date("Y-m-d");

    // Path to your CSV file
    $file = 'form_submissions.csv';

    // Check if the file exists and count lines to determine the next ID
    $next_id = 1;
    if (file_exists($file)) {
        $csv = array_map('str_getcsv', file($file));
        $next_id = count($csv); // Get the next ID as the current row count (including header)
    }

    // Open file in append mode, create file if not exists
    $file_handle = fopen($file, 'a');
    
    if (!$file_handle) {
        echo "Error opening the file!";
        exit;
    }

    // If file is empty, add a header row
    if (filesize($file) == 0) {
        fputcsv($file_handle, array('ID', 'Name', 'Email', 'Phone', 'Message','Date'));
    }

    // Write form data to the file as a CSV entry with the auto-incremented ID
   $csv_data = array($next_id, $name, $email, $phone, $message, $date);
    fputcsv($file_handle, $csv_data);

    // Close the file
    fclose($file_handle);

    // Generate HTML table with the submitted data, including the ID
    $html_table = "
        <h3>Godrej Noida 44</h3>
        <table border='1' cellpadding='5' cellspacing='0'>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
            <tr>
                <td>{$next_id}</td>
                <td>{$name}</td>
                <td>{$email}</td>
                <td>{$phone}</td>
                <td>{$message}</td>
                <td>{$date}</td>
            </tr>
        </table>
    ";

    // Initialize PHPMailer
    $mail = new PHPMailer(true); // Create a new PHPMailer instance

    try {
        // SMTP Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'boprealty37@gmail.com';
        $mail->Password = 'oiev gauw jwip povp'; // Use App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('boprealty37@gmail.com', 'Mailer');
        $mail->addAddress('boprealty37@gmail.com', 'Bop Realty Pvt Ltd');

        // Create email content in table format
        $mail->isHTML(true);
        $mail->Subject = 'Project Name: GODREJ 44';
        $mail->Body    = "Name: $name<br>Email: $email<br>Phone: $phone<br>Message: $message<br>Date: $date<br>" . $html_table;

        // Send the email
        $mail->send();

        // Redirect to thankyou.html after sending email
        header('Location: thankyou.html');
        exit; // Terminate the script after redirecting

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>