<?php
include_once 'Game.php';
$game = new Game();


// Handle the play form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['play'])) {
    // Check if both 'piece' and 'to' are set in $_POST
    if (isset($_POST['piece']) && isset($_POST['to'])) {
        $piece = $_POST['piece'];
        $to = $_POST['to'];
        $game->play($piece, $to);
    }
}

// Handle the move form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['move'])) {
    if (isset($_POST['from']) && isset($_POST['to'])) {
        $from = $_POST['from'];
        $to = $_POST['to'];
        $game->move($from, $to);

    }
}

// Handle the pass form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass'])) {
    $game->pass();

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restart'])) {
    $game->restart();

}

// Handle the undo form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['undo'])) {
    $game->undo();

}

?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Hive</title>
        <style>
            div.board {
                width: 60%;
                height: 100%;
                min-height: 500px;
                float: left;
                overflow: scroll;
                position: relative;
            }

            div.board div.tile {
                position: absolute;
            }

            div.tile {
                display: inline-block;
                width: 4em;
                height: 4em;
                border: 1px solid black;
                box-sizing: border-box;
                font-size: 50%;
                padding: 2px;
            }

            div.tile span {
                display: block;
                width: 100%;
                text-align: center;
                font-size: 200%;
            }

            div.player0 {
                color: black;
                background: white;
            }

            div.player1 {
                color: white;
                background: black
            }

            div.stacked {
                border-width: 3px;
                border-color: red;
                padding: 0;
            }
        </style>
    </head>
    <body>
    <div class="board">
        <?php

        $min_p = 1000;
        $min_q = 1000;
        foreach ($game->getBoard() as $pos => $tile) {
            $pq = explode(',', $pos);
            if ($pq[0] < $min_p) $min_p = $pq[0];
            if ($pq[1] < $min_q) $min_q = $pq[1];
        }
        foreach (array_filter($game->getBoard()) as $pos => $tile) {
            $pq = explode(',', $pos);
            $pq[0];
            $pq[1];
            $h = count($tile);
            echo '<div class="tile player';
            echo $tile[$h - 1][0];
            if ($h > 1) echo ' stacked';
            echo '" style="left: ';
            echo ($pq[0] - $min_p) * 4 + ($pq[1] - $min_q) * 2;
            echo 'em; top: ';
            echo ($pq[1] - $min_q) * 4;
            echo "em;\">($pq[0],$pq[1])<span>";
            echo $tile[$h - 1][1];
            echo '</span></div>';
        }
        ?>
    </div>
    <div class="hand">
        White:
        <?php
        foreach ($game->getPlayerHand(0) as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player0"><span>' . $tile . "</span></div> ";
            }
        }
        ?>
    </div>
    <div class="hand">
        Black:
        <?php
        foreach ($game->getPlayerHand(1) as $tile => $ct) {
            for ($i = 0; $i < $ct; $i++) {
                echo '<div class="tile player1"><span>' . $tile . "</span></div> ";
            }
        }
        ?>
    </div>
    <div class="turn">
        Turn: <?php if ($game->getCurrentPlayerIndex() == 0) echo "White"; else echo "Black"; ?>
    </div>
    <form method="post">
        <select name="piece">
            <?php
            $playerHand = $game->getPlayerHand($game->getCurrentPlayerIndex());
            foreach ($playerHand as $tile => $ct) {
                if ($ct > 0) { // Only show pieces with count greater than 0
                    echo "<option value=\"$tile\">$tile</option>";
                }
            }
            ?>
        </select>
        <select name="to">
            <?php
            $possiblePositions = $game->getPossiblePossitions();
            foreach ($possiblePositions as $pos) {
                if (!$game->getBoard()[$pos]) { // Only show positions that are empty
                    echo "<option value=\"$pos\">$pos</option>";
                }
            }
            ?>
        </select>
        </select>
        <input type="submit" name="play" value="Play">
    </form>
    <form method="post">
        <select name="from">
            <?php
            foreach ($game->getCurrentPlayerPositions() as $pos) {
                echo "<option value=\"$pos\">$pos</option>";
            }
            ?>
        </select>
        <select name="to">
            <?php
            $possiblePositions = $game->getPossiblePossitions();
            foreach ($possiblePositions as $pos) {
                if (!$game->getBoard()[$pos]) { // Only show positions that are empty
                    echo "<option value=\"$pos\">$pos</option>";
                }
            }
            ?>
        </select>
        <input type="submit" name="move" value="Move">
    </form>
    <form method="post">
        <input type="submit" name="pass" value="Pass">
    </form>
    <form method="post">
        <input type="submit" name="restart" value="Restart">
    </form>
    <strong><?php if (isset($_SESSION['error'])) echo($_SESSION['error']);
        unset($_SESSION['error']); ?></strong>
    <ol>
        <?php
        $result = $game->getCurrentGame($game->getGameId());
        while ($row = $result->fetch_array()) {
            echo '<li>' . $row[2] . ' ' . $row[3] . ' ' . $row[4] . '</li>';
        }
        ?>
    </ol>
    <form method="post">
        <input type="submit" name="undo" value="Undo">
    </form>
    </body>
    </html>

<?php
