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
        $this->game = new Game($db, false);
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


        $aiMove = $this->game->aiMove();
        var_dump($aiMove);
    }

}