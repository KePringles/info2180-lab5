<?php
// world.php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

try {
  $conn = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $username,
    $password,
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  echo "<p>DB connection error.</p>";
  exit;
}

$country = isset($_GET['country']) ? trim($_GET['country']) : '';

if ($country !== '') {
  $stmt = $conn->prepare("
    SELECT name, continent, independence_year, head_of_state
    FROM countries
    WHERE name LIKE CONCAT('%', :country, '%')
    ORDER BY name ASC
  ");
  $stmt->execute([':country' => $country]);
} else {
  $stmt = $conn->query("
    SELECT name, continent, independence_year, head_of_state
    FROM countries
    ORDER BY name ASC
  ");
}

$lookup = isset($_GET['lookup']) ? trim($_GET['lookup']) : '';
// If looking up cities
if ($lookup === "cities" && $country !== "") {

   $stmt = $conn->prepare("
       SELECT cities.name AS city_name, cities.district, cities.population
       FROM cities
       JOIN countries ON cities.country_code = countries.code
       WHERE countries.name LIKE CONCAT('%', :country, '%')
       ORDER BY cities.population DESC
   ");

   $stmt->execute([':country' => $country]);
   $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

   // Output cities table
   if (!$rows) {
       echo "<p>No cities found for that country.</p>";
       exit;
   }

   echo '<table class="countries">';
   echo '<thead><tr>
           <th>City</th>
           <th>District</th>
           <th>Population</th>
       </tr></thead><tbody>';

   foreach ($rows as $r) {
       $name  = htmlspecialchars($r['city_name']);
       $dist  = htmlspecialchars($r['district']);
       $pop   = htmlspecialchars($r['population']);

       echo "<tr>
               <td data-label=\"City\">$name</td>
               <td data-label=\"District\">$dist</td>
               <td data-label=\"Population\">$pop</td>
             </tr>";
   }

   echo '</tbody></table>';
   exit;
}


$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output as an HTML table (Exercise 4)
if (!$rows) {
  echo "<p>No matching countries found.</p>";
  exit;
}

echo '<table class="countries">';
echo '  <thead>';
echo '    <tr>';
echo '      <th>Country</th>';
echo '      <th>Continent</th>';
echo '      <th>Independence Year</th>';
echo '      <th>Head of State</th>';
echo '    </tr>';
echo '  </thead>';
echo '  <tbody>';

foreach ($rows as $r) {
  $name  = htmlspecialchars($r['name'] ?? '', ENT_QUOTES, 'UTF-8');
  $cont  = htmlspecialchars($r['continent'] ?? '', ENT_QUOTES, 'UTF-8');
  $iyear = htmlspecialchars($r['independence_year'] !== null ? $r['independence_year'] : '—', ENT_QUOTES, 'UTF-8');
  $hos   = htmlspecialchars($r['head_of_state'] !== null ? $r['head_of_state'] : '—', ENT_QUOTES, 'UTF-8');

  echo "    <tr>
          <td data-label=\"Country\">{$name}</td>
          <td data-label=\"Continent\">{$cont}</td>
          <td data-label=\"Independence Year\">{$iyear}</td>
          <td data-label=\"Head of State\">{$hos}</td>
        </tr>";

}

echo '  </tbody>';
echo '</table>';

