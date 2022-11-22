<div id="contact-form" class="contact-form"><a id="contact-form"></a>
  {{ $nameLen := 3 }}
  {{ $msgLen  := 20 }}
  <?php
  $submitted    = false;
  $success      = false;
  $submitFail   = false;
  $errors       = false;
  $errorMessage = '';
  $name         = "";
  $email        = "";
  $message      = "";
  $page         = "{{ .Page.Title }}";
  $honeyPot     = "";
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $submitted = true;
    $name      = htmlspecialchars(stripslashes(trim($_POST['name'])));
    $email     = htmlspecialchars(stripslashes(trim($_POST['email'])));
    $phone     = htmlspecialchars(stripslashes(trim($_POST['phone'])));
    $message   = htmlspecialchars(stripslashes(trim($_POST['message'])));
    $honeyPot  = htmlspecialchars(stripslashes(trim($_POST['note'])));
    if (empty($name)) {
      $name_error = 'Name is empty';
      $errors = true;
    } elseif (strlen($name) < {{ $nameLen }}) {
      $name_error = 'Name too short, (should be at least {{ $nameLen }} characters)';
    }
    if (empty($email)) {
      $email_error = 'Email is empty';
      $errors = true;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email_error = 'Email is invalid';
      $errors = true;
    }
    if (!empty($phone) && !filter_var($phone, FILTER_SANITIZE_NUMBER_INT)) {
      $phone_error = 'Phone is invalid';
      $errors = true;
    }
    if (empty($message)) {
      $message_error = 'Message is empty';
      $errors = true;
    } elseif (strlen($message) < {{ $msgLen }}) {
      $name_error = 'Message too short, (should be at least {{ $msgLen }} characters)';
    }
    if( ! empty( $honeypot ) ){
      echo "<p>You entered text into a hidden field, form not submitted.<p>";
      $errors = true;
    }
    $data = array('secret' => $ini['hcaptcha']['secret'],
                  'response' => $_POST['h-captcha-response']);
    $verify = curl_init();
    curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
    curl_setopt($verify, CURLOPT_POST, true);
    curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($verify);
    $responseData = json_decode($response, true);
    if (! $responseData['success'] ) {
        $captcha_error = "hCaptcha challenge failed: " . join(', ', $responseData['error-codes']);
        $errors = true;
    }
    if (! $errors ) {
        $toEmail = 'info@weefreemedic.org';
        $emailSubject = 'New email from ' . $page . ' contact form';
        $bodyParagraphs = ["Name: {$name}", "Email: {$email}", "Phone: {$phone}", "Page: {$page}", "Message:", nl2br($message)];
        $body = join("<br>", $bodyParagraphs);

        $mail = new PHPMailer(true);
        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = $ini['smtp']['host'];                   //Set the SMTP server to send through
            $mail->Port       = $ini['smtp']['port'];;                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $ini['home']['from'];                   //SMTP username
            $mail->Password   = $ini['home']['password'];               //SMTP password

            //Recipients
            $mail->setFrom($ini['home']['from'], 'Wee Free Home Page');
            $mail->AddReplyTo($email, $name);
            $mail->addAddress('info@weefreemedic.org', 'Wee Free Info');

            //Content
            $mail->isHTML(true);
            $mail->Subject = $emailSubject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            $success = true;
            $successMessage = '<p class="form-success">Message sent</p>';
        } catch (Exception $e) {
          $errorMessage = join('<br>', [
                "<p class='form-error'>Message could not be sent. ",
                "Please email me at info@weefreemedic.org or try again later. ",
                "Mailer Error: {$mail->ErrorInfo}</p>",
                "<p>", $body, "</p>"
          ]);
        }
    } else {
        $errorMessage = join(PHP_EOL, [
          "<div id='form-error' class='text-center'>",
          "<p>There was a problem submitting the form.</p>",
          "<p class='form-error'>Please fix errors below and resubmit.</p>",
          "</div>"]
        );
    }
  }
  if ($submitted && ! $success) { $submitFail = true; };
  ?>
  <script src="/plugins/angular/angular.min.js"></script>
  <script type="text/javascript">
    var app = angular.module('ContactForm', [])
    app.controller('MyController', function ($scope) {
    });
  </script>
  <div ng-app="ContactForm" ng-controller="MyController">
    <form name="ContactForm" id="contact-form" method="post" action="{{ path.Dir .Page.RelPermalink }}/#contact-form" ng-submit="MyForm.$valid" novalidate>
      <?php if ($submitted) {
        if ($submitFail) {
          echo $errorMessage;
        } else {
          echo $successMessage;
        }
      } ?>
      <div class="form-group mb-4 pb-2">
        <label for="exampleFormControlInput1" class="form-label">
          Full Name
          <span ng-style="ContactForm.name.$dirty && ContactForm.name.$error.required && {'color': 'red'}">*Required &nbsp; </span>
          <span class="form-error" ng-show="ContactForm.name.$dirty && ! ContactForm.name.$error.required && !ContactForm.name.$valid">Too short, minimum {{ $nameLen }}</span>
        </label>
        <input
          type="text" name="name" id="name" ng-model="name" class="form-control shadow-none"
          ng-class="{'is-invalid': ContactForm.name.$dirty && !ContactForm.name.$valid, 'is-valid': ContactForm.name.$dirty && ContactForm.name.$valid}"
          required minlength="{{ $nameLen }}" ng-minlength="{{ $nameLen }}" placeholder="Your Full Name"
          <?php if($submitFail) echo "value='{$name}'" ?>
        >
        <?php if(isset($name_error)) echo "<p class='invalid-feedback'>" . $name_error . "<p>"; ?>
      </div>
      <div class="form-group mb-4 pb-2">
        <label for="exampleFormControlInput1" class="form-label">
          Email address
          <span ng-style="ContactForm.email.$dirty && ContactForm.email.$error.required && {'color': 'red'}">*Required &nbsp; </span>
          <span class="form-error" ng-show="!ContactForm.email.$error.required && ContactForm.email.$invalid">*Invalid Email Address</span>
        </label>
        <input type="email" class="form-control shadow-none" name="email" id="email" ng-model="EmailAddress"
        ng-class="{'is-invalid': ContactForm.email.$dirty && !ContactForm.email.$valid}"
        required placeholder="email@example.com" <?php if($submitFail) echo "value='{$email}'" ?>>
        <?php if(isset($email_error)) echo "<p class='form-error'>" . $email_error . "<p>"; ?>
      </div>
      <div class="form-group mb-4 pb-2">
        <label for="exampleFormControlInput1" class="form-label">
          Phone number (optional)
          <span class="form-error" ng-show="ContactForm.phone.$invalid"> *Number too short</span>
        </label>
        <input type="phone" class="form-control shadow-none" name="phone" id="phone" ng-model="mobileNo"
        ng-class="{'is-invalid': ContactForm.phone.$dirty && !ContactForm.phone.$valid}"
        minlength=10 ng-minlength=10 placeholder="000-000-0000" <?php if($submitFail) echo "value='{$phone}'" ?>>
        <?php if(isset($phone_error)) echo "<p class='form-error'>" . $phone_error . "<p>"; ?>
      </div>
      <div><input name="note" type="text" id="note" class="jkdlsn"></div>
      <div class="form-group mb-4 pb-2">
        <label for="exampleFormControlTextarea1" class="form-label">
          Write Message
          <span ng-style="ContactForm.message.$dirty && ContactForm.message.$error.required && {'color': 'red'}">*Required &nbsp; </span>
          <span class="form-error" ng-show="ContactForm.message.$dirty && !ContactForm.message.$valid">Too short, minimum {{ $msgLen }}</span>
        </label>
        <textarea class="form-control shadow-none" ng-class="{'is-invalid': ContactForm.message.$dirty && !ContactForm.message.$valid}" name="message" id="message" ng-model="message" required minlength="{{ $msgLen }}" ng-minlength="{{ $msgLen }}" rows="3"><?php if($submitFail) echo "$message" ?></textarea>
        <?php if(isset($message_error)) echo "<p class='form-error'>" . $message_error . "<p>"; ?>
      </div>
      <div class="center text-center">
        <?php if(isset($captcha_error)) echo "<p class='form-error'>" . $captcha_error . "<p>"; ?>
        <div class="h-captcha" data-sitekey='<?php echo $ini["hcaptcha"]["site"]?>'></div>
      </div>
      <div><button class="btn btn-primary w-100" type="submit" ng-disabled="ContactForm.$invalid">Send Message</button></div>
    </form>
  </div>
</div>
