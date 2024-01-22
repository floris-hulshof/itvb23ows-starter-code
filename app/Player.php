<?php

class Player
{
    private $name;
    private $hand;

    public function __construct($name) {
        $this->name = $name;
        $this->hand = new Hand();
    }

    public function getName() {
        return $this->name;
    }
    public function getHand(){
        return $this->hand;
    }
    public function setHand($hand){
        $this->hand = $hand;
    }


}