---
title: "Contact Form"
description: "Contact form submitted"
draft: false
headerImage: sf-banner.jpg
---
<?php
$errors = [];
$errorMessage = '';
if (!empty($_POST)) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $page = $_POST['page'];
    if (empty($name)) {
        $errors[] = 'Name is empty';
    }
    if (empty($email)) {
        $errors[] = 'Email is empty';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid';
    }
    if (empty($message)) {
        $errors[] = 'Message is empty';
    }
    if (empty($errors)) {
        $toEmail = 'info@weefreemedic.org';
        $emailSubject = 'New email from ' . $page . ' contant form';
        $headers = ['From' => $email, 'Reply-To' => $email, 'Content-type' => 'text/html; charset=iso-8859-1'];
        $bodyParagraphs = ["Name: {$name}", "Email: {$email}", "Page:", $page, "Message:", $message];
        $body = join(PHP_EOL, $bodyParagraphs);
        if (mail($toEmail, $emailSubject, $body, $headers)) {
            header('Location: thank-you.html');
        } else {
            $failMessage = "Oops, couldn't send message. Please email me at info@weefreemedic.org or try again later";
            $errorMessage = join(PHP_EOL, [$failMessage, $body])
        }
    } else {
        $allErrors = join('<br/>', $errors);
        $errorMessage = "<p style='color: red;'>{$allErrors}</p>";
    }
}
?>

<?php
if (!empty($errors)) {
   $allErrors = join('<br/>', $errors);
   $errorMessage = "<p style='color: red;'>{$allErrors}</p>";
}
?>
