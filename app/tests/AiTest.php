<?php
require_once __DIR__ . '/../Game.php';
include_once "DatabaseMock.php";



use PHPUnit\Framework\TestCase;

class AiTest extends TestCase
{
    private $game;
    public function __construct(string $name)
    {
        parent::__construct($name);
        $db= new DatabaseMock();
        $this->game = new Game($db, true);
    }


    public function testAiIsFirstMove(){
//        $aiMock = $this->getMockBuilder(\App\Ai::class)->disableOriginalConstructor()
//            ->getMock();
        //when game restarts ai will immediately play and switch to player 2
        $this->game->testRestart();
        $currentPlayer = $this->game->getCurrentPlayerIndex();
        $expectedPlayer = 1;
        $this->assertEquals($expectedPlayer, $currentPlayer);
    }

    public function testAiFirstMove(){
        $board = [];
        $currentPlayerIndex = 0;
        $game_id = 9999999;  // hardcoded testId
        $hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        //Because game->restart starts with a aiMove() it is not possible to get the move from restart only on the board
        $aiMock = $this->getMockBuilder(\App\Ai::class)->disableOriginalConstructor()
            ->getMock();

        $aiMock->move($currentPlayerIndex, $board, $hand);

        $predictedMove = ["play", "Q", "0,0"];
        $aiMove = $this->game->aiMove();

        $this->assertEquals($predictedMove, $aiMove);
    }

}