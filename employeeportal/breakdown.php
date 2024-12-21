<?php
$id = $_SESSION['idno'];
$sqlProfile = mysqli_query($con, "SELECT * FROM employee_profile WHERE idno='$id'");
$profile = mysqli_fetch_array($sqlProfile);
$idno = $profile['idno'];

// Query to fetch offenses and points
$sqlPointsBreakdown = mysqli_query($con, "SELECT offense, points FROM points WHERE idno='$idno'");

// Initialize total points and breakdown array
$total_points = 0;
$breakdown = [];

// Translation mapping
$translations = [
    "12" => "Code A",
    "13" => "Code A",
    "65" => "Code B-",
    "15" => "Code D",
    "16" => "Code E",
    "17" => "Code F",
    "22" => "Code I-",
    "19" => "Code L",
    "63" => "Code L-",
    "62" => "Code W",
    "66" => "Code M"
];

if (mysqli_num_rows($sqlPointsBreakdown) > 0) {
    while ($row = mysqli_fetch_assoc($sqlPointsBreakdown)) {
        // Translate offense if a match is found
        $translated_offense = isset($translations[$row['offense']]) ? $translations[$row['offense']] : $row['offense'];
        
        $breakdown[] = [
            'offense' => $translated_offense,
            'points' => (float)$row['points']
        ];
        $total_points += (float)$row['points'];
    }
} else {
    $total_points = 0;
}

// Format total points to 1 decimal place
$total_points = number_format((float)$total_points, 1, '.', '');

// Output the breakdown

echo "<ul>";
foreach ($breakdown as $item) {
    echo "<li>" . htmlspecialchars($item['offense']) . ": " . number_format($item['points'], 1, '.', '') . "</li>";
}
echo "</ul>";


?>
