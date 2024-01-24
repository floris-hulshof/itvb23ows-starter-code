<?php

class State {

    public function getState(Game $game) {
        return serialize([$game->currentPlayer()->getHand(), $game->board, $game->currentPlayer()]);
    }

    public function setState(Game $game,$state) {
        list($a, $b, $c) = unserialize($state);
        $game->currentPlayer()->setHand($a);
        $game->setBoard($b);
        $game->setCurrentPlayerIndex($c);

    }
}