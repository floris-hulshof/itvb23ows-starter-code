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

        $aiMock = $this->getMockBuilder(\App\Ai::class)->disableOriginalConstructor()
            ->getMock();

        $hand = [0 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3], 1 => ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3]];
        $aiMove =$aiMock->move($this->game->getCurrentPlayerIndex(), $hand, $this->game->getBoard());

        $predictedMove = ["play", "Q", "0,0"];
        var_dump($predictedMove);
        var_dump($aiMove);

        $this->assertEquals($predictedMove, $aiMove);
    }

}