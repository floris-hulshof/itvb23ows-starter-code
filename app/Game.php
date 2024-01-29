<?php
include_once "Db.php";
include_once "State.php";

class Game
{
    private $hand;
    private $board;
    private $game_id;
    private $db;
    private $currentPlayerIndex;
    private $state;
    private $offsets = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];


    public function __construct()
    {
        session_start();
        $this->db = new Db();

        if (!isset($_SESSION['board'])) {
            $this->restart(); // niet heel netjes maar anders wordt dit dupecode
        }
        $this->board = $_SESSION['board'];
        $this->currentPlayerIndex = $_SESSION['player'];
        $this->hand = $_SESSION['hand'];
        $this->game_id = $_SESSION['game_id'];

        $this->state = new State;
    }

    public function getOffsets()
    {
        return $this->offsets;
    }

    public function undo()
    {
        $result = $this->db->undoDB($_SESSION['last_move']);
        $_SESSION['last_move'] = $result[5];
        $this->state->setState($result[6]);
        $this->hand = $_SESSION['hand'];
        $this->board = $_SESSION['board'];
        $this->currentPlayerIndex = $_SESSION['player'];

    }

    public function getPlayerHand($index)
    {
        return $this->hand[$index];
    }

    public function switchPlayer()
    {
        $this->currentPlayerIndex = 1 - $this->currentPlayerIndex;
    }

    public function getBoard()
    {
        return $this->board;
    }

    public function setBoard($to, $piece)
    {
        $this->board[$to] = [[$this->currentPlayerIndex, $piece]];
    }


    public function getCurrentPlayerIndex()
    {
        return $this->currentPlayerIndex;
    }

    public function setCurrentPlayerIndex($currentPlayerIndex)
    {
        $this->currentPlayerIndex = $currentPlayerIndex; //This function only exists to satisfy the Stage class...
    }

    public function getGameId()
    {
        return $this->game_id;
    }

    public function getCurrentGame($game_id)
    {

        return $this->db->getCurrentGameDB($game_id);
    }

    public function play($piece, $to)
    {

        $player = $this->currentPlayerIndex;
        $board = $this->board;
        $hand = $this->hand[$player];

        if (!isset($hand[$piece]) || $hand[$piece] <= 0) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($this->board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($this->board) && !$this->hasNeighBour($to)) {
            $_SESSION['error'] = "board position has no neighbour";
        } elseif (array_sum($hand) < 11 && !$this->neighboursAreSameColor($to)) {  //TODO: check this if statement for the <11
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif (array_sum($hand) <= 8 && isset($hand['Q']) && $piece != "Q") {   //Fix voor bug 4
            $_SESSION['error'] = 'Must play queen bee';
        } else {

            var_dump(array_sum($hand));
            $this->setBoard($to, $piece);
            $this->hand[$player][$piece]--;
            $this->switchPlayer();
            $_SESSION['board'] = $this->board;
            $_SESSION['hand'] = $this->hand;
            $_SESSION['player'] = $this->currentPlayerIndex;
            $state = $this->state->getState();
            $lastId = $this->db->playDB($this->game_id, $piece, $to, $_SESSION['last_move'], $state);
            $_SESSION['last_move'] = $lastId;
        }

    }

    public function move($from, $to)
    {
        $player = $this->currentPlayerIndex;
        $board = $this->board;
        $hand = $this->hand[$player];

        unset($_SESSION['error']);

        if (!isset($board[$from]))
            $_SESSION['error'] = 'Board position is empty';
        elseif ($board[$from][count($board[$from]) - 1][0] != $player)
            $_SESSION['error'] = "Tile is not owned by player";
        elseif (isset($hand['Q']) && $hand['Q'] > 0)
            $_SESSION['error'] = "Queen bee is not played";
        else {
            $tile = array_pop($board[$from]);
            if (!$this->hasNeighBour($to))
                $_SESSION['error'] = "Move would split hive";
            else {
                $all = array_keys($board);
                $queue = [array_shift($all)];
                while ($queue) {
                    $next = explode(',', array_shift($queue));
                    foreach ($this->offsets as $pq) {
                        list($p, $q) = $pq;
                        $p += $next[0];
                        $q += $next[1];
                        if (in_array("$p,$q", $all)) {
                            $queue[] = "$p,$q";
                            $all = array_diff($all, ["$p,$q"]);
                        }
                    }
                }
                if ($all) {
                    $_SESSION['error'] = "Move would split hive";
                } else {
                    if ($from == $to) $_SESSION['error'] = 'Tile must move';
                    elseif (isset($board[$to]) && $tile[1] != "B") $_SESSION['error'] = 'Tile not empty';
                    elseif ($tile[1] == "Q" || $tile[1] == "B") {
                        if (!$this->slide($from, $to))
                            $_SESSION['error'] = 'Tile must slide';
                    }
                }
            }
            if (isset($_SESSION['error'])) {
                if (isset($board[$from])) array_push($board[$from], $tile);
                else $board[$from] = [$tile];
            } else {
                if (isset($board[$to])) array_push($board[$to], $tile);
                else $board[$to] = [$tile];
                unset($board[$from]); // Fixed place piece on position
                $this->switchPlayer();
                $_SESSION['player'] = $this->currentPlayerIndex;
                // change this to go to DB
                $state = $this->state->getState();
                $lastId = $this->db->moveDB($from, $to, $_SESSION['last_move'], $state);
                $_SESSION['last_move'] = $lastId;
            }


            $this->board = $board;
            $_SESSION['board'] = $this->board;
        }
    }

    public function pass()
    {

        $state = $this->state->getState();
        $lastId = $this->db->passDB($_SESSION['game_id'], $_SESSION['last_move'], $state);
        $this->switchPlayer();
        $_SESSION['last_move'] = $lastId;
        $_SESSION['player'] = $this->currentPlayerIndex;

    }


    public function isNeighbour($a, $b)
    {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) return true;
        if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) return true;
        if ($a[0] + $a[1] == $b[0] + $b[1]) return true;
        return false;
    }

    public function hasNeighBour($a)
    {
        foreach (array_keys($this->board) as $b) {
            if ($this->isNeighbour($a, $b)) return true;
        }
    }

    public function neighboursAreSameColor($a)
    {
        foreach ($this->board as $b => $st) {
            if (!$st) continue;
            $c = $st[count($st) - 1][0];
            if ($c != $this->getCurrentPlayerIndex() && $this->isNeighbour($a, $b)) return false;
        }
        return true;
    }

    public function len($tile)
    {
        return $tile ? count($tile) : 0;
    }

    public function slide($from, $to)
    {

        $board = $this->board;

        if (!$this->hasNeighbour($to) || !$this->isNeighbour($from, $to)) {
            return false;
        }

        $b = explode(',', $to);

        $common = [];
        foreach ($this->offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p.",".$q)) {
                $common[] = $p.",".$q;
            }
        }

        if (
            (!isset($board[$common[0]]) || !$board[$common[0]]) &&
            (!isset($board[$common[1]]) || !$board[$common[1]]) &&
            (!isset($board[$from]) || !$board[$from]) &&
            (!isset($board[$to]) || !$board[$to])
        ) {
            return false;
        }

        $firstCommonLen = isset($board[$common[0]]) ? $board[$common[0]] : 0;
        $firstCommonLen = $this->len($firstCommonLen);

        $secondCommonLen = isset($board[$common[1]]) ? $board[$common[1]] : 0;
        $secondCommonLen = $this->len($secondCommonLen);

        $fromLen = isset($board[$from]) ? $board[$from] : 0;
        $fromLen = $this->len($fromLen);

        $toLen = isset($board[$to]) ? $board[$to] : 0;
        $toLen = $this->len($toLen);

        return min($firstCommonLen, $secondCommonLen)
            <= max($fromLen, $toLen);
    }

    public function isValidPosition($position)
    {
        $player = $this->currentPlayerIndex;

        // Check if it's the second turn
        if (count($this->getCurrentPlayerPositions()) < 1) {
            return true; // Allow any position on the second turn
        }


        // Check if the position is next to any of the current player's tiles
        foreach ($this->getBoard() as $pos => $tiles) {
            foreach ($tiles as $tile) {
                if ($tile[0] === $player && $this->isNeighbour($pos, $position)) {
                    // Check if the position is not adjacent to any opponent's tiles
                    foreach ($this->getBoard() as $opponentPos => $opponentTiles) {
                        foreach ($opponentTiles as $opponentTile) {
                            if ($opponentTile[0] !== $player && $this->isNeighbour($opponentPos, $position)) {
                                return false; // Disallow placement next to opponent's tile on the second turn
                            }
                        }
                    }
                    return true;
                }
            }
        }

        return false;
    }

    public function getPossiblePositions()
    {
        $validPositions = [];

        // Get all possible positions
        foreach ($this->getOffsets() as $pq) {
            foreach (array_keys($this->getBoard()) as $pos) {
                $pq2 = explode(',', $pos);
                $possiblePosition = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);

                // Check if the position is valid for the current player
                if ($this->isValidPosition($possiblePosition)) {
                    $validPositions[] = $possiblePosition;
                }
            }
        }
        if (!count($validPositions) && empty($this->getBoard())) {
            $validPositions[] = '0,0';
        }

        return array_unique($validPositions);
    }

    public function getMovePositions()
    {
        $openPositions = [];

        // Iterate over the board
        foreach ($this->getOffsets() as $pq) {
            foreach (array_keys($this->getBoard()) as $pos) {
                $pq2 = explode(',', $pos);
                $possiblePosition = ($pq[0] + $pq2[0]) . ',' . ($pq[1] + $pq2[1]);

                // Check if the position is next to any tiles on the board
                $isValid = false;
                foreach ($this->getBoard() as $boardPos => $tiles) {
                    foreach ($tiles as $tile) {
                        $tilePlayer = $tile[0];
                        if ($this->isNeighbour($boardPos, $possiblePosition)) {
                            $isValid = true;
                            break;
                        }
                    }
                }

                // If the position is valid, add it to open positions
                if ($isValid) {
                    $openPositions[] = $possiblePosition;
                }
            }
        }

        // If the board is empty, allow placing a piece at 0,0
        if (empty($this->getBoard())) {
            $openPositions[] = '0,0';
        }

        return array_unique($openPositions);
    }


    public function getCurrentPlayerPositions()
    {
        $player = $this->currentPlayerIndex;
        $currentPlayerPositions = [];

        foreach ($this->getBoard() as $pos => $tiles) {
            foreach ($tiles as $tile) {
                if ($tile[0] === $player) {
                    $currentPlayerPositions[] = $pos;
                }
            }
        }

        return $currentPlayerPositions;
    }


    public function restart()
    {
        $this->board = [];
        $this->currentPlayerIndex = 0;
        $this->game_id = $this->db->saveGame();  // saves in Game Id the last saved game
        $this->hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $_SESSION['OFFSET'] = $this->offsets;
        $_SESSION['board'] = $this->board;
        $_SESSION['hand'] = $this->hand;
        $_SESSION['player'] = $this->getCurrentPlayerIndex();
        $_SESSION["game_id"] = $this->game_id;

    }


}