<?php
include_once "Game.php";
include_once "DatabaseMock.php";

use PHPUnit\Framework\TestCase;


class GrasshopperTest extends TestCase
{
    private $game;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $db= new DatabaseMock();
        $this->game = new Game($db, false);
    }

    public function testGrassHopperSameMoveFromToPosition(){
        $this->game->testRestart();

        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('0,-1', "G");
        $this->game->switchPlayer();
        $this->game->setBoard('0,2', "B");
        $this->game->switchPlayer();

        $result = $this->game->antSoldierMove("0,-1", "0,3");

        $this->assertTrue($result);
    }


    public function testGrasshopperMoveToNonLinearPosition(){
        $this->game->testRestart();

        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('0,-1', "G");
        $this->game->switchPlayer();
        $this->game->setBoard('0,2', "B");
        $this->game->switchPlayer();

        $result = $this->game->grasshopperMove("0,-1","1,2");

        $this->assertFalse($result);
    }
    public function testGrasshopperMoveToOcupiedPosition(){
        $this->game->testRestart();

        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('0,-1', "G");
        $this->game->switchPlayer();
        $this->game->setBoard('0,2', "B");
        $this->game->switchPlayer();

        $result = $this->game->grasshopperMove("0,-1", "0,0");

        $this->assertFalse($result);
    }

}