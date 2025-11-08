<?php
// world.php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  echo "<p>DB connection error.</p>";
  exit;
}

$country = isset($_GET['country']) ? trim($_GET['country']) : '';

if ($country !== '') {
  $stmt = $conn->prepare("SELECT name, head_of_state FROM countries WHERE name LIKE CONCAT('%', :country, '%')");
  $stmt->execute([':country' => $country]);
} else {
  $stmt = $conn->query("SELECT name, head_of_state FROM countries");
}

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output a simple HTML fragment (Exercise 3 expects HTML)
echo "<ul>";
foreach ($results as $row) {
  $name = htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8');
  $hos  = htmlspecialchars($row['head_of_state'] ?? '—', ENT_QUOTES, 'UTF-8');
  echo "<li>{$name} is ruled by {$hos}</li>";
}
echo "</ul>";
