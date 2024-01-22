<?php

session_start();

include_once 'util.php';

class Game
{
    private $db;
    private $players;
    private $board;
    private $hand;
    private $gameId;
    private $lastMove;

    public function __construct()
    {
        $this->db = include 'database.php';
        $this->players = [new Player("white"), new Player("black")];
        $this->board = [];
//        $this->player = $_SESSION['player'];
//        $this->board = $_SESSION['board'];

        $this->hand = $_SESSION['hand'][$this->player];
        $this->gameId = $_SESSION['game_id'];
        $this->lastMove = $_SESSION['last_move'];
    }

    public function playMove($piece, $to)
    {
        if (!$this->hand[$piece]) {
            $_SESSION['error'] = "Player does not have tile";
        } elseif (isset($this->board[$to])) {
            $_SESSION['error'] = 'Board position is not empty';
        } elseif (count($this->board) && !$this->hasNeighbour($to)) {
            $_SESSION['error'] = "Board position has no neighbour";
        } elseif (array_sum($this->hand) < 11 && !$this->neighboursAreSameColor($to)) {
            $_SESSION['error'] = "Board position has opposing neighbour";
        } elseif (array_sum($this->hand) <= 8 && $this->hand['Q']) {
            $_SESSION['error'] = 'Must play queen bee';
        } else {
            $this->updateGameState($piece, $to);
            $this->updateDatabase();
        }

        header('Location: index.php');
    }

    private function hasNeighbour($position)
    {
        return hasNeighBour($position, $this->board);
    }

    private function neighboursAreSameColor($position)
    {
        return neighboursAreSameColor($this->player, $position, $this->board);
    }

    private function updateGameState($piece, $to)
    {
        $_SESSION['board'][$to] = [[$this->player, $piece]];
        $_SESSION['hand'][$this->player][$piece]--;
        $_SESSION['player'] = 1 - $this->player;
        $this->lastMove = $_SESSION['last_move'] = $this->db->insert_id;
    }

    private function updateDatabase()
    {
        $stmt = $this->db->prepare('insert into moves (game_id, type, move_from, move_to, previous_id, state) values (?, "play", ?, ?, ?, ?)');
        $stmt->bind_param('issis', $this->gameId, $piece, $to, $this->lastMove, get_state());
        $stmt->execute();
    }
}

$game = new Game();
$game->playMove($_POST['piece'], $_POST['to']);
