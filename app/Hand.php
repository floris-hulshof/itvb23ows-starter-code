<?php

class Hand
{
    private $pieces;
    public function __construct() {
        $this->pieces = ["Q" => 1, "B" => 2, "S" => 2, "A" => 3, "G" => 3];
    }


    public function getPieces() {
        return $this->pieces;
    }

    public function removePiece($pieceType) {
        if (isset($this->pieces[$pieceType])) {
            if ($this->pieces[$pieceType] > 1) {
                $this->pieces[$pieceType]--;
            } else {
                unset($this->pieces[$pieceType]);
            }
        }
    }

}