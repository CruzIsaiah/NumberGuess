<!DOCTYPE html>
<html>

<head>
  <title>Number Guessing Game</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <h1>Can you guess the number?</h1>

  <?php
  session_start();

  $numberRange = array(1, 10);

  // Define the maximum guesses as constants
  define('EASY_MAX_GUESSES', 3);
  define('HARD_MAX_GUESSES', 1);

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mode'])) {
    $_SESSION['mode'] = $_POST['mode'];
    $_SESSION['number'] = rand($numberRange[0], $numberRange[1]);
    $_SESSION['guesses'] = 0;
    $_SESSION['guessedNumbers'] = array();
    $_SESSION['message'] = "Number of guesses: " . getMaxGuesses();
    $_SESSION['error'] = "";
  }

  // Function to get the maximum guesses based on the selected mode
  function getMaxGuesses()
  {
    return $_SESSION['mode'] === 'easy' ? EASY_MAX_GUESSES : HARD_MAX_GUESSES;
  }

  if (isset($_SESSION['mode'])) {
    // Game logic
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guess'])) {
      $userGuess = $_POST["guess"];
      $_SESSION['guesses']++;

      // Initialize guessedNumbers as an empty array if not set
      if (!isset($_SESSION['guessedNumbers'])) {
        $_SESSION['guessedNumbers'] = array();
      }

      // Check if the guessed number is already present in the guessedNumbers array
      if (in_array($userGuess, $_SESSION['guessedNumbers'])) {
        $_SESSION['error'] = "You already guessed that number. Try a different one.";
      } else {
        $_SESSION['error'] = "";

        // Add the guessed number to the guessedNumbers array
        $_SESSION['guessedNumbers'][] = $userGuess;

        if ($userGuess > $_SESSION['number']) {
          $_SESSION['message'] = "Too high! Try again. Tries left: " . (getMaxGuesses() - $_SESSION['guesses']);
        } elseif ($userGuess < $_SESSION['number']) {
          $_SESSION['message'] = "Too low! Try again. Tries left: " . (getMaxGuesses() - $_SESSION['guesses']);
        } else {
          $_SESSION['message'] = "Good job! You got it!";
          $_SESSION['number'] = null;
        }

        if ($_SESSION['guesses'] >= getMaxGuesses()) {
          $_SESSION['message'] = "Sorry, better luck next time.";
          $_SESSION['number'] = null;
        }
      }
    }
  }
  ?>

  <?php if (!isset($_SESSION['mode'])) : ?>
    <!-- Mode selection form -->
    <div class="container">
      <form method="POST" action="">
        <p>Select Game Mode:</p>
        <label for="easyMode">Easy Mode</label>
        <input type="radio" name="mode" value="easy" id="easyMode" checked>
        <label for="hardMode">Hard Mode</label>
        <input type="radio" name="mode" value="hard" id="hardMode">
        <br>
        <input type="submit" value="Start Game">
      </form>
    </div>
  <?php endif; ?>

  <?php if (isset($_SESSION['mode'])) : ?>
    <div class="container">
      <!-- Game messages and form -->
      <?php if ($_SESSION['number'] !== null) : ?>
        <p><?php echo $_SESSION['message']; ?></p>
        <form method="POST" action="">
          <label for="guess">Guess a number between 1 and 10:</label>
          <input type="number" name="guess" min="1" max="10" required>
          <input type="submit" value="Submit">
        </form>

        <?php if (isset($_SESSION['error']) && $_SESSION['error']) : ?>
          <p class="error"><?php echo $_SESSION['error']; ?></p>
        <?php endif; ?>

        <?php if (isset($_SESSION['guessedNumbers']) && $_SESSION['guessedNumbers']) : ?>
          <div class="number-bank">
            <h3>Number Bank:</h3>
            <ul>
              <?php foreach ($_SESSION['guessedNumbers'] as $guessedNumber) : ?>
                <li><?php echo $guessedNumber; ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($_SESSION['number'] === null) : ?>
        <p class="final-message">
          <?php if ($_SESSION['guesses'] >= getMaxGuesses()) : ?>
            Sorry, better luck next time.
          <?php else : ?>
            Good job! You got it!
          <?php endif; ?>
        </p>
        <form method="POST" action="">
          <input type="hidden" name="restart" value="1">
          <input type="submit" value="Play Again">
        </form>
        <?php
        // Reset the game data when restarting
        unset($_SESSION['guesses']);
        unset($_SESSION['message']);
        unset($_SESSION['guessedNumbers']);
        unset($_SESSION['error']);
        unset($_SESSION['mode']);
        ?>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</body>

</html>
