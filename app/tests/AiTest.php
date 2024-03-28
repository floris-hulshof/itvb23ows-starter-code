<?php
require_once __DIR__ . '/../Game.php';
include_once "DatabaseMock.php";



use PHPUnit\Framework\TestCase;

class AiTest extends TestCase
{
    private $game;
    protected function setUp(): void
    {
        $db = new DatabaseMock();
        $this->game = new Game($db, false);
    }

    public function testAiIsFirstMove(){

        //when game restarts ai will immediately play and switch to player 2
        $aiMock = $this->getMockBuilder(\App\Ai::class)->disableOriginalConstructor()
            ->getMock();
        $this->game->aiMove();
        $currentPlayer = $this->game->getCurrentPlayerIndex();
        $expectedPlayer = 1;
        $this->assertEquals($expectedPlayer, $currentPlayer);
    }

    public function testAiFirstMove(){

        var_dump($this->game->getCurrentPlayerIndex());
        var_dump($this->game->getBoard());
        $aiMove = $this->game->aiMove();
        $predictedMove = ["play", "Q", "0,0"];


        $this->assertEquals($predictedMove, $aiMove);
    }
//    public function testAiFirstRoundIsAlwaysPlayMove(){
//
//    }

}