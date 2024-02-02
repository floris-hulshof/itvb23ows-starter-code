<?php
include_once "app/Game.php";
include_once "tests/DatabaseMock.php";

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private $game;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $db= new DatabaseMock();
        $this->game = new Game($db);
    }

    //To check if this move is not in the center of surrounding peaces
    public function testCheckIfMoveToIsNotInTheCenter(){
        $this->game->testRestart();

        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('-1,0', "A");
        $this->game->switchPlayer();
        $this->game->setBoard('-1,2', "A");
        $this->game->switchPlayer();
        $this->game->setBoard("-2,1", "S");
        $this->game->switchPlayer();
        $this->game->setBoard("-2,3", "A");
        $this->game->switchPlayer();
        $this->game->setBoard("-1, -1", "B");
        $this->game->switchPlayer();


        $result = $this->game->checkifMoveIsNotSurrounded("-1,1");

        $this->assertFalse($result);
    }

}