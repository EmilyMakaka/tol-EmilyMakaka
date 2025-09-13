<?php
require 'ClassAutoLoad.php'; // loads conf.php and layout objects

global $conf;
$mysqli = new mysqli($conf['db_host'], $conf['db_user'], $conf['db_pass'], $conf['db_name']);
if ($mysqli->connect_error) {
    die('DB connection error: ' . $mysqli->connect_error);
}

$res = $mysqli->query("SELECT name, email, created_at FROM users ORDER BY name ASC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Signed-up users</title>
</head>
<body>
  <h1>Signed-up users</h1>
  <?php if ($res && $res->num_rows > 0): ?>
    <ol>
      <?php while ($row = $res->fetch_assoc()): ?>
        <li>
          <?php echo htmlspecialchars($row['name']); ?> — <?php echo htmlspecialchars($row['email']); ?>
          <small>(joined <?php echo htmlspecialchars($row['created_at']); ?>)</small>
        </li>
      <?php endwhile; ?>
    </ol>
  <?php else: ?>
    <p>No users yet.</p>
  <?php endif; ?>
  <p><a href="signup.php">Back to signup</a></p>
</body>
</html>
<?php
$mysqli->close();
?>