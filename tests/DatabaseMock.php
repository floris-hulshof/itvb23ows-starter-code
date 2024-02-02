<?php

class DatabaseMock
{
    public function __construct()
    {
    }
    public function undoDB($last_move)
    {
        //$this->set_state($result[6]);
    }

    public function getLastGameId()
    {
    }
    public function saveGame(){

    }
    public function playDB($game_id, $piece, $to, $lastMove, $state){

    }
    public function moveDB($from, $to, $last_move, $state)
    {

    }

    public function passDB($game_id, $last_move, $state)
    {

    }
    public function getCurrentGameDB($game_id){
    }

    public function getMoveId($id){

    }
    public function deleteMoveId($id){

    }

}