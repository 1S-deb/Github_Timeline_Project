<?php
session_start();

require_once 'functions.php';

$successMessage = '';
$errorMessage = '';

//Step 1: User submits email to unsubscribe
if($_SERVER['REQUEST_METHOD']==='POST'&& isset($_POST['unsubscribe_email']))
{
    $email =trim($_POST['unsubscribe_email']);
    if(filter_var($email,FILTER_VALIDATE_EMAIL))
    {
        $code=generateVerificationCode();
        $_SESSION['unsubscribe_code']=$code;
        $_SESSION['unsubscribe_email']=$email;

        //Send unsubscription code
        $subject ="Confirm Unsubscription";
        $message="<p>To confirm unsubscription,use this code:<strong>$code</strong></p>";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: no-reply@example.com";

        if(mail($email,$subject,$message,$headers))
        {
            $successMessage="Verification code sent to $email.";
        }
    
        else{
            $errorMessage="Failed to send unsubscription email.";
        }
    }
    else{
        $errorMessage ="Invalid email address.";
    }
}
//Step 2:User submits verification code
 if(isset($_POST['unsubscribe_verification_code']))
 {
    $enteredCode=trim($_POST['unsubscribe_verification_code']);
    if(
        isset($_SESSION['unsubscribe_code'],$_SESSION['unsubscribe_email']) &&
        $enteredCode===$_SESSION['unsubscribe_code']
    )
    {
        $email =$_SESSION['unsubscribe_email'];
        if(unsubscribeEmail($email)){
            $successMessage ="Email $email has been unsubscribed.";
        }
        else{
            $errorMessage ="Failed to unsubscribe email.";
        }
        unset($_SESSION['unsubscribe_code'],$_SESSION['unsubscribe_email']);
    }
    else{
        $errorMessage ="Invalid unsubscription code.";
    }
 }
// TODO: Implement the form and logic for email unsubscription.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  
    <title>Unsubscribe</title>
</head>
<body>
    <h2>Unsubscribe from Github Timeline Updates</h2>
    <?php if($successMessage):?>
        <p style="color:green;"><strong><?=$successMessage?></strong></p>
    <?php endif;?>
    <?php if($errorMessage): ?>
    <p style="color:red;"><strong><?= $errorMessage ?></strong></p>
    <?php endif; ?>
    <!--Unsubscribe Email Form-->
    <form method="POST" action="">
        <label for="unsubscribe_email">Enter your email to unsubscribe:</label><br>
        <input type="email" name="unsubscribe_email"required>
        <button type="submit" id="submit-unsubscribe">Unsubscribe</button>
    </form>

    <br><br>
    <!--Unsubscribe Verification Code Form-->
    <form method="POST" action="">
        <label for="unsubscribe_verification_code">Enter unsubscription code:</label><br>
        <input type="text" name="unsubscribe_verification_code" maxlength="6" required>
        <button type="submit" id="verify-unsubscribe">Verify</button>
    </form>
</body>
</html>
