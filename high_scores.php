<?php
$conn = new mysqli("localhost", "root", "", "memory_game");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT player_name, attempts, time_taken, date FROM high_scores ORDER BY attempts, time_taken LIMIT 10";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>High Scores</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>High Scores</h1>

<table>
    <tr>
        <th>Player</th>
        <th>Attempts</th>
        <th>Time Taken (seconds)</th>
        <th>Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['player_name'] ?></td>
            <td><?= $row['attempts'] ?></td>
            <td><?= $row['time_taken'] ?></td>
            <td><?= $row['date'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

</body>
</html>

<?php $conn->close(); ?>
