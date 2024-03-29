<?php
require_once __DIR__ . '/../Game.php';
include_once "DatabaseMock.php";

use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    private $game;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $db= new DatabaseMock();
        $this->game = new Game($db, false);
    }

    //To check if this move is not in the center of surrounding peaces
    public function testMoveToIsNotSurrounded(){
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
    public function testCheckIfBoardIsEmpty(){
        $this->game->testRestart();

        $result = $this->game->getBoard();
        $this->assertEmpty($result);
    }
    public function testMoveIsNotSurrounded(){
        $this->game->testRestart();

        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('1,-1', "B");
        $this->game->switchPlayer();
        $this->game->setBoard('1,1', "B");
        $this->game->switchPlayer();
        $this->game->setBoard("1,-2", "B");
        $this->game->switchPlayer();
        $this->game->setBoard("2,0", "B");
        $this->game->switchPlayer();

        $result = $this->game->checkifMoveIsNotSurrounded("1,0");

        $this->assertFalse($result);
    }

    public function testPlayerZeroStart(){
        $this->game->testRestart();
        $result = $this->game->getCurrentPlayerIndex();
        $this->assertEquals( $result, 0);
    }
    public function testChangePlayer(){
        $this->game->testRestart();
        $beforePlayerSwitch = $this->game->getCurrentPlayerIndex();
        $this->game->switchPlayer();
        $afterPlayerSwitch = $this->game->getCurrentPlayerIndex();

        $this->assertNotEquals($beforePlayerSwitch, $afterPlayerSwitch);

    }

    public function testMovePositions(){
        $this->game->testRestart();
        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('1,-1', "B");

        $expectedPositions = ['-1,0', '-1,1', '-1,2', '0,-1', '0,0', '0,1', '0,2', '1,-1', '1,-2', '1,0', '1,1', '2,-1', '2,-2'];

        $actualPositions = $this->game->getMovePositions();
        sort($expectedPositions);
        sort($actualPositions);

        $this->assertEquals($expectedPositions, $actualPositions);
    }
    public function testPlayerPositions(){
        $this->game->testRestart();
        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', "Q");
        $this->game->switchPlayer();
        $this->game->setBoard('1,-1', "B");

        $expectedPositions = ["0,0", "1,-1"];
        $actualPositions = $this->game->getCurrentPlayerPositions();
        sort($expectedPositions);
        sort($actualPositions);
        $this->assertEquals($expectedPositions, $actualPositions);
    }
    public function testOffsets(){
        $this->game->testRestart();
        $offsets = $this->game->getOffsets();
        $expectedoffsets = [[0, 1], [0, -1], [1, 0], [-1, 0], [-1, 1], [1, -1]];

        $this->assertEquals($expectedoffsets, $offsets);
    }
    public function testGameWon(){
        $this->game->testRestart();
        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', 'A');
        $this->game->setBoard('-1,1', 'A');
        $this->game->setBoard('-1,0', 'A');
        $this->game->setBoard('0,-1', 'A');
        $this->game->setBoard('1,-1', 'A');
        $this->game->setBoard('1,0', 'A');

        $this->assertTrue($this->game->isGameWon($this->game->getCurrentPlayerIndex()));

    }
    public function testIsGameDraw(){
        $this->game->restart();
        $this->game->testRestart();
        $this->game->setBoard('0,0', 'Q');
        $this->game->switchPlayer();
        $this->game->setBoard('0,1', 'Q');
        $this->game->setBoard('-1,1', 'A');
        $this->game->setBoard('-1,0', 'A');
        $this->game->setBoard('0,-1', 'A');
        $this->game->setBoard('1,-1', 'A');
        $this->game->setBoard('1,0', 'A');
        $this->game->setBoard('1,1', 'A');
        $this->game->setBoard('0,2', 'A');
        $this->game->setBoard('-1,2', 'A');
        $player1 = $this->game->isGameWon($this->game->getCurrentPlayerIndex());
        $this->game->switchPlayer();
        $player2 = $this->game->isGameWon($this->game->getCurrentPlayerIndex());

        $this->assertTrue($this->game->isGameDraw($player1, $player2));
    }
}