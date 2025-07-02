<?php
session_start();


require_once 'functions.php';
$successMessage = '';
$errorMessage = '';
// TODO: Implement the form and logic for email registration and verification
// Step 1: Email submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        $_SESSION['verification_code'] = $code;
        $_SESSION['pending_email'] = $email;

        if (sendVerificationEmail($email, $code)) {
            $successMessage = "Verification code sent to $email.";
            //echo "<p><strong>DEBUG Code:</strong> $code</p>";
        } else {
            $errorMessage = "Failed to send verification email.";
        }
    } else {
        $errorMessage = "Invalid email address.";
    }
}

// Step 2: Code verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    $enteredCode = trim($_POST['verification_code']);
    if (
        isset($_SESSION['verification_code'], $_SESSION['pending_email']) &&
        $enteredCode === $_SESSION['verification_code']
    ) {
        $registered = registerEmail($_SESSION['pending_email']);
        if ($registered) {
            $successMessage = "Email {$_SESSION['pending_email']} successfully registered";
        } else {
            $errorMessage = "Failed to register email.";
        }

        unset($_SESSION['verification_code']);
        unset($_SESSION['pending_email']);
    } else {
        $errorMessage = "Invalid verification code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
</head>
<body>
    <h2>Subscribe to GitHub Timeline Updates</h2>

    <!-- Success or Error Message -->
    <?php if ($successMessage): ?>
        <p style="color:green;"><strong><?php echo $successMessage; ?></strong></p>
    <?php endif; ?>
    <?php if ($errorMessage): ?>
        <p style="color:red;"><strong><?php echo $errorMessage; ?></strong></p>
    <?php endif; ?>

    <!-- Email Input Form -->
    <form method="POST" action="">
        <label for="email">Enter your email:</label><br>
        <input type="email" name="email" required>
        <button type="submit" id="submit-email">Submit</button>
    </form>

    <br><br>

    <!-- Code Input Form -->
    <form method="POST" action="">
        <label for="verification_code">Enter verification code:</label><br>
        <input type="text" name="verification_code" maxlength="6" required>
        <button type="submit" id="submit-verification">Verify</button>
    </form>
</body>
</html>

