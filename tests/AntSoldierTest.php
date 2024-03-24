<?php
include_once "app/Game.php";
include_once "tests/DatabaseMock.php";

use PHPUnit\Framework\TestCase;

class AntSoldierTest extends TestCase{
    private $game;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $db= new DatabaseMock();
        $this->game = new Game($db);
    }
    public function testAntSoldierSameMoveFromToPosition(){
        $this->game->testRestart();

        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('0,-1', "A");
        $this->game->switchPlayer();
        $this->game->setBoard('0,2', "A");
        $this->game->switchPlayer();

        $result = $this->game->antSoldierMove("0,-1", "0,-1");

        $this->assertFalse($result);

    }

    public function testAntSoldierMoveToIsNotEmpty(){
        $this->game->testRestart();
        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('0,-1', "A");
        $this->game->switchPlayer();
        $this->game->setBoard('0,2', "A");
        $this->game->switchPlayer();

        $result = $this->game->antSoldierMove("0,-1", "0,2");
        $this->assertFalse($result);
    }

    public function testAntSoldierMoveToTileWithNoNeighbour(){
        $this->game->testRestart();
        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('0,-1', "A");
        $this->game->switchPlayer();
        $this->game->setBoard('0,2', "A");
        $this->game->switchPlayer();

        $result = $this->game->antSoldierMove("0,-1", "0, 4");

        $this->assertFalse($result);
    }
}
