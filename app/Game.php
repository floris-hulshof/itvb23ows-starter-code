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

        $this->lastMove = 0; //TODO: Check how Last move works in the vanilla code
        $this->state = new State;
//        $this->hand = $_SESSION['hand'][$this->player]; // Hand does not need to be initialized because it is done when player is initialized

        //$this->lastMove = $_SESSION['last_move']; // Last move needs to be added when a play is made
    }

    public function undo(){
        $result = $this->db->undoDB($this);
        $this->lastMove = $result[5];
        $this->state->setState($this, $result[6]);
    }

    public function getCurrentPlayer(){
        return $this->player[$this->currentPlayerIndex];
    }
    public function switchPlayer(){
        $this->currentPlayerIndex = 1 - $this->currentPlayerIndex;
    }
    public function getBoard(){
        return $this->board;
    }
    public function setBoard($board){
        $this->board = $board;
    }


    public function getLastMove()
    {
        return $this->lastMove;
    }
    public function setLastMove($lastMove){
        $this->lastMove = $lastMove;
    }

    public function setCurrentPlayerIndex($currentPlayerIndex){
        $this->currentPlayerIndex = $currentPlayerIndex; //This function only exists to satisfy the Stage class...
    }

    public function move(){

    }


    function isNeighbour($a, $b) {
        $a = explode(',', $a);
        $b = explode(',', $b);
        if ($a[0] == $b[0] && abs($a[1] - $b[1]) == 1) return true;
        if ($a[1] == $b[1] && abs($a[0] - $b[0]) == 1) return true;
        if ($a[0] + $a[1] == $b[0] + $b[1]) return true;
        return false;
    }
    function hasNeighBour($a) {
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

    function len($tile) {
        return $tile ? count($tile) : 0;
    }

    function slide($from, $to) {
        if (!$this->hasNeighBour($to)) return false;
        if (!$this->isNeighbour($from, $to)) return false;
        $b = explode(',', $to);
        $common = [];
        foreach ($this->offsets as $pq) {
            $p = $b[0] + $pq[0];
            $q = $b[1] + $pq[1];
            if ($this->isNeighbour($from, $p.",".$q)) $common[] = $p.",".$q;
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