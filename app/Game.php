<?php


class Game
{
    private $player;
    private $board;
    private $game_id;
    private $db;
    private $lastMove;
    private $currentPlayerIndex;
    private $state;
    private $offsets = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];


    public function __construct()
    {
        $this->db = new Db();
        $this->player = [new Player("white"), new Player("black")];
        $this->board = [];
        $this->game_id = $this->db->getLastGameId() + 1;  // get last game id and add 1
        $this->currentPlayerIndex = 0;

        $this->lastMove = null; //TODO: Check how Last move works in the vanilla code
        $this->state = new State;
//        $this->hand = $_SESSION['hand'][$this->player]; // Hand does not need to be initialized because it is done when player is initialized

        //$this->lastMove = $_SESSION['last_move']; // Last move needs to be added when a play is made
    }

    public function undo()
    {
        $result = $this->db->undoDB($this);
        $this->lastMove = $result[5];
        $this->state->setState($this, $result[6]);
    }

    public function currentPlayer()
    {
        return $this->player[$this->currentPlayerIndex];
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
        $this->board[$to] = [$this->currentPlayerIndex, $piece];
    }


    public function getLastMove()
    {
        return $this->lastMove;
    }

    public function setLastMove($lastMove)
    {
        $this->lastMove = $lastMove;
    }

    public function setCurrentPlayerIndex($currentPlayerIndex)
    {
        $this->currentPlayerIndex = $currentPlayerIndex; //This function only exists to satisfy the Stage class...
    }

    public function play($piece, $to)
    {
        if (!$this->currentPlayer()->hasPieceInHand($piece)) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($this->board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($this->board) && !$this->hasNeighBour($to)) {
            $_SESSION['error'] = "board position has no neighbour";
        } elseif (array_sum($this->currentPlayer()->getHand()->getPieces()) < 11 && !$this->neighboursAreSameColor($to)) {  //TODO: check this if statement for the <11
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif (array_sum($this->currentPlayer()->getHand()->getPieces()) <= 8 && !$this->currentPlayer()->getHand()->hasPiece('Q')) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $this->setBoard($to, $piece);
            $this->currentPlayer()->getHand()->removePiece($piece);
            $this->switchPlayer();
            $state = $this->state->getState($this);
            $lastId = $this->db->playDB($this->game_id, $piece, $to, $this->lastMove, $state);
            $this->lastMove = $lastId;
        }

    }

    public function move($from, $to)
    {
        $player = $this->currentPlayerIndex;
        $board = $this->board;
        $hand = $this->currentPlayer()->getHand()->getPieces();

        unset($_SESSION['error']);

        if (!isset($board[$from]))
            $_SESSION['error'] = 'Board position is empty';
        elseif ($board[$from][count($board[$from]) - 1][0] != $player)
            $_SESSION['error'] = "Tile is not owned by player";
        elseif ($hand['Q'])
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
                        if (!slide($board, $from, $to))
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
                $this->switchPlayer();
                // change this to go to DB
                $state = $this->state->getState($this);
                $lastId = $this->db->moveDB($from, $to, $this->lastMove, $state);
                $this->lastMove = $lastId;
            }
            $this->board = $board;
        }
    }

    public function pass()
    {
        $state = $this->state->getState($this);
        $lastId = $this->db->passDB($this->game_id, $this->lastMove, $state);
        $this->lastMove = $lastId;
        $this->switchPlayer();
    }


    function isNeighbour($a, $b)
    {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) return true;
        if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) return true;
        if ($a[0] + $a[1] == $b[0] + $b[1]) return true;
        return false;
    }

    function hasNeighBour($a)
    {
        foreach (array_keys($this->board) as $b) {
            if (isNeighbour($a, $b)) return true;
        }
    }

    public function neighboursAreSameColor($a)
    {
        foreach ($this->board as $b => $st) {
            if (!$st) continue;
            $c = $st[count($st) - 1][0];
            if ($c != $this->getCurrentPlayer() && $this->isNeighbour($a, $b)) return false;
        }
        return true;
    }

    function len($tile)
    {
        return $tile ? count($tile) : 0;
    }

    function slide($from, $to)
    {
        if (!$this->hasNeighBour($to)) return false;
        if (!$this->isNeighbour($from, $to)) return false;
        $b = explode(',', $to);
        $common = [];
        foreach ($this->offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p . "," . $q)) $common[] = $p . "," . $q;
        }
        if (!$this->board[$common[0]] && !$this->board[$common[1]] && !$this->board[$from] && !$this->board[$to]) return false;
        return min($this->len($this->board[$common[0]]), $this->len($this->board[$common[1]])) <= max($this->len($this->board[$from]), $this->len($this->board[$to]));
    }


    public function restart()
    {
        $this->board = [];
        $this->player = [new Player("white"), new Player("black")];
        $this->currentPlayerIndex = 0;
        $this->db->saveGame();


    }
}