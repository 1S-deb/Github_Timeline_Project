<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    // TODO: Implement this function
    return str_pad(strval(rand(0,999999)),6,'0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    // TODO: Implement this function
    $subject ="Your Verification Code";
    $message="<p>Your verification code is :<strong>$code</strong></p>";
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: no-reply@example.com";


    return mail($email ,$subject, $message,$headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
  $emails= file_exists($file) ? file($file,FILE_IGNORE_NEW_LINES) : [];
    // TODO: Implement this function
    if(!in_array($email,$emails))
    {
      return file_put_contents($file, $email . PHP_EOL, FILE_APPEND) !==false;
    }
    return true;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    if(!file_exists($file)) return false;

    $emails =file($file,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
    $updatedEmails=array_filter($emails, fn($e)=>strtolower(trim($e)) !== strtolower(trim($email)));
    return file_put_contents($file , implode(PHP_EOL,$updatedEmails) . PHP_EOL) !==false;

}

/**
 * Fetch GitHub timeline.
 */
function fetchGitHubTimeline() {
    // TODO: Implement this function
    // $url ="https://www.github.com/timeline";
    // $ch = curl_init($url);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    // curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Client');
    // $response= curl_exec($ch);
    // curl_close($ch);
    // return $response;
    $url = "https://api.github.com/events"; // ✅ GitHub's public event API
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'GitHub-Timeline-App'); // GitHub requires this
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/vnd.github.v3+json'
    ]);
     $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        error_log('cURL error: ' . curl_error($ch));
        curl_close($ch);
        return json_encode([]); // return empty JSON array to avoid crash
    }

    curl_close($ch);
    return $response;
}

/**
 * Format GitHub timeline data. Returns a valid HTML sting.
 */
function formatGitHubData(array $data): string {
    // TODO: Implement this function
    //return "<h2>Github Timeline Updates</h2>" .
          // "<p>Raw timeline data was fetched as HTML and cannot be structured.</p>";
    $html = "<h2>GitHub Timeline Updates</h2>";
    $html .= "<table border='1'>";
    $html .= "<tr><th>Event</th><th>User</th></tr>";

    foreach ($data as $event) {
        $type = $event['type'] ?? 'N/A';
        $user = $event['actor']['login'] ?? 'N/A';

        $html .= "<tr><td>{$type}</td><td>{$user}</td></tr>";
    }

    $html .= "</table>";

    return $html;
}



/**
 * Send the formatted GitHub updates to registered emails.
 */
function sendGitHubUpdatesToSubscribers(): void {
  $file = __DIR__ . '/registered_emails.txt';
    // TODO: Implement this function
    $emails =array_map('trim',file($file , FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
    
    $response = fetchGitHubTimeline();
    $data = json_decode($response, true);
    // If data is not valid JSON, skip sending
    if (!$data || !is_array($data)) {
        return;
    }

    $htmlTable = formatGitHubData($data);
    foreach($emails as $email)
    {
      $unsubscribeLink ="http://localhost:8000/unsubscribe.php?email=" . urldecode($email);

      $message=$htmlTable .
                
                "<p><a href=\"$unsubscribeLink\" id=\"unsubscribe-button\">Unsubscribe</a></p>";

      $subject ="Latest Github Updates";
      $headers = "MIME-Version: 1.0\r\n";
      $headers .= "Content-type:text/html;charset=UTF-8\r\n";
      $headers .= "From: no-reply@example.com";
      
       mail($email, $subject, $message, $headers);
      
    }
}
