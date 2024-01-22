<?php

class State {

    public function getState(Game $game) {
        return serialize([$game->getCurrentPlayer()->getHand(), $game->board, $game->getCurrentPlayer()]);
    }

    public function setState(Game $game,$state) {
        list($a, $b, $c) = unserialize($state);
        $game->getCurrentPlayer()->setHand($a);
        $game->setBoard($b);
        $game->setCurrentPlayerIndex($c);

    }
}