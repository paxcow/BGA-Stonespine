<?php

namespace States;

require_once(__DIR__ . '/../Helpers/actionManager.inc.php');

trait Improvement
{

    function stImprovementPhase()
    {

        $transition = (\Helpers\Players::haveGold() > 0) ? "activatePlayer" : "everyonePassed";

        $this->gamestate->nextState($transition);
    }

    function stActivateRichestPlayer()
    {
        $richest = \Helpers\Players::getRichest();
        $this->gamestate->changeActivePlayer($richest);
        $this->gamestate->nextState();

    }

    function argPurchaseableTokens()
    {
        $active_player = $this->getActivePlayerId();
        $gold = \Helpers\Players::getGold($active_player);
        $market_cards = $this->market_cards->getCardsInLocation("table");
    }


}
