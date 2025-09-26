<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'ClassAutoLoad.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

// Validation
if ($name === '') {
    $errors[] = 'Name is required.';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'A valid email is required.';
}
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}

if (!empty($errors)) {
    echo "<h3>There were errors:</h3><ul>";
    foreach ($errors as $err) {
        echo "<li>" . htmlspecialchars($err) . "</li>";
    }
    echo "</ul><p><a href='signup.php'>Go back to signup</a></p>";
    exit;
}

// Connect to DB
global $conf;
$mysqli = new mysqli(
    $conf['db_host'],
    $conf['db_user'],
    $conf['db_pass'],
    $conf['db_name']
);

if ($mysqli->connect_error) {
    die('DB connection error: ' . $mysqli->connect_error);
}

// Check duplicate email
$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $stmt->close();
    echo "<p>Email already registered. <a href='signup.php'>Back</a></p>";
    exit;
}
$stmt->close();

// Insert user
$hash = password_hash($password, PASSWORD_DEFAULT);
$ins  = $mysqli->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
$ins->bind_param('sss', $name, $email, $hash);

if (!$ins->execute()) {
    echo "DB insert failed: " . htmlspecialchars($ins->error);
    $ins->close();
    $mysqli->close();
    exit;
}
$userId = $ins->insert_id;
$ins->close();

// ✅ Build verification link
$verifyLink = "http://localhost/verify.php?id=" . $userId . "&token=" . md5($email);

// ✅ Email content
$mailCnt = [
    'name_from' => 'ICS 2.2',
    'mail_from' => 'emily.makaka@strathmore.edu',   
    'name_to'   => $name,
    'mail_to'   => $email,
    'subject'   => 'Welcome to ICS 2.2! Account Verification',
    'body'      => "Hello " . htmlspecialchars($name) . ",<br><br>
                    You requested an account on ICS 2.2.<br><br>
                    In order to use this account you need to 
                    <a href='$verifyLink'>Click Here</a> 
                    to complete the registration process.<br><br>
                    Regards,<br>
                    Systems Admin<br>
                    ICS 2.2"
];

// Create mail object & send
if (!isset($ObjSendMail)) {
    $ObjSendMail = new SendMail();
}

$sendResult = $ObjSendMail->SendMail($name, $email);
 // pass conf + mail content

// Show success page
echo "<div style='font-family: Arial, sans-serif; margin: 40px; padding:20px; border:1px solid #ccc; border-radius:8px; max-width:600px;'>
        <h2>Signup Successful ✅</h2>
        <p>Hello <b>" . htmlspecialchars($name) . "</b>,</p>
        <p>Your account has been created successfully.</p>";

if ($sendResult === true) {
    echo "<p>A verification email has been sent to <b>" . htmlspecialchars($email) . "</b>.</p>";
} else {
    echo "<p>User registered but email could not be sent.</p>
          <p><small>Error: " . htmlspecialchars($sendResult) . "</small></p>";
}

echo "<p><a href='signin.php'>Click here to login after verification</a></p>
      </div>";

$mysqli->close();
?>
