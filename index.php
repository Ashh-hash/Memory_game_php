<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize or resume the game session
session_start();

// Initialize the game if it hasn't started yet
if (!isset($_SESSION['game_board'])) {
    initializeGame();
}

// Initialize the game board with shuffled pairs
function initializeGame() {
    $cards = array_merge(range(1, 8), range(1, 8)); // 8 pairs of cards
    shuffle($cards); // Shuffle the cards to randomize their positions
    $_SESSION['game_board'] = $cards; // Store shuffled cards in session
    $_SESSION['revealed'] = array_fill(0, 16, false); // Store card states: hidden (false) or revealed (true)
    $_SESSION['first_card'] = -1;  // To store the first card's index
    $_SESSION['second_card'] = -1; // To store the second card's index
    $_SESSION['attempts'] = 0;     // Number of attempts
    $_SESSION['start_time'] = time(); // Time when the game starts
    $_SESSION['waiting'] = false; // State to handle mismatches
}

// Handle the card flip
if (isset($_POST['flip'])) {
    $index = $_POST['flip'];

    // Only allow flipping hidden cards
    if ($_SESSION['revealed'][$index] === false) {
        // If no cards are flipped or one card is already flipped
        if ($_SESSION['first_card'] === -1) {
            // First card flip
            $_SESSION['first_card'] = $index;
        } elseif ($_SESSION['second_card'] === -1 && $_SESSION['first_card'] !== $index) {
            // Second card flip
            $_SESSION['second_card'] = $index;
            $_SESSION['attempts']++;  // Count the attempt only when both cards are flipped

            // Check if the cards match
            if ($_SESSION['game_board'][$_SESSION['first_card']] === $_SESSION['game_board'][$_SESSION['second_card']]) {
                // Cards match, keep them revealed
                $_SESSION['revealed'][$_SESSION['first_card']] = true;
                $_SESSION['revealed'][$_SESSION['second_card']] = true;

                // Reset for the next round
                $_SESSION['first_card'] = -1;
                $_SESSION['second_card'] = -1;
            } else {
                // If the cards don't match, wait until the next round to reset
                $_SESSION['waiting'] = true; // Set a waiting state
            }
        }
    }
}

// Handle waiting state for mismatched cards
if (isset($_SESSION['waiting']) && $_SESSION['waiting'] === true) {
    // If the cards don't match, reset them for the next move
    $_SESSION['first_card'] = -1;
    $_SESSION['second_card'] = -1;
    $_SESSION['waiting'] = false; // Reset the waiting state
}

// Reset the game
if (isset($_POST['reset'])) {
    initializeGame();
}

// Check if the game is over
$gameOver = !in_array(false, $_SESSION['revealed']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memory Matching Game</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Memory Matching Game</h1>

<form method="POST">
    <div class="game-board">
        <?php for ($i = 0; $i < 16; $i++): ?>
            <?php if ($_SESSION['revealed'][$i] || $i === $_SESSION['first_card'] || $i === $_SESSION['second_card']): ?>
                <!-- Display revealed cards -->
                <div class="card revealed"><?= $_SESSION['game_board'][$i] ?></div>
            <?php else: ?>
                <!-- Display hidden cards as buttons -->
                <button type="submit" name="flip" value="<?= $i ?>" class="card">?</button>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</form>

<div class="scoreboard">
    <p>Attempts: <?= $_SESSION['attempts'] ?></p>
    <p>Time: <?= time() - $_SESSION['start_time'] ?> seconds</p>
</div>

<?php if ($gameOver): ?>
    <p>Congratulations! You matched all the pairs!</p>
<?php endif; ?>

<form method="POST">
    <button type="submit" name="reset">Restart Game</button>
</form>

</body>
</html>
