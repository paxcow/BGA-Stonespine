<?php

namespace States;

use Classes\Dungeon;

require_once(__DIR__ . '/../Helpers/actionManager.inc.php');

trait Improvement
{

    function stImprovementPhase()
    {
        $players = $this->loadPlayersBasicInfos();

        foreach ($players as $player_id => $player) {
            $dungeons[$player_id] = new \Classes\Dungeon($player_id);
            $gold[$player_id] = $dungeons[$player_id]->getIncome($this);
        }


        /*         foreach($players as $player_id => $player){
            $this->notifyAllPlayers("receive_income",clienttranslate('${player_name} receives ${gold} coin(s).'), array(
                "player_id" => $player_id,
                "player_name" => $player["player_name"],
                "gold" => $gold[$player_id]
            ));
            $this->notifyPlayer($player_id,"receive_income",clienttranslate('You receive ${gold} coin(s).'), array(
                "player_id" => $player_id,
                "player_name" => $player["player_name"],
                "gold" => $gold[$player_id]
            ));
        } */



        $this->gamestate->nextState("activatePlayer");
    }

    function stActivateRichestPlayer()
    {
        $richest = \Helpers\Players::getRichest();
        $this->gamestate->changeActivePlayer($richest);
        $this->gamestate->nextState("playOrPass");
    }

    function argPurchaseableTokens()
    {

        $active_player = $this->getActivePlayerId();

        //get player's available gold
        $gold = \Helpers\Players::getGold($active_player);

        //create market cards
        $basic_market_cards = $this->market_cards->getCardsInLocation("table");
        $market_cards = array_map(
            function ($card) {
                return new \Classes\Market($card);
            },
            $basic_market_cards
        );
        //list all purchaseable sections
        $affordable_purchases = array();
        foreach ($market_cards as $card) {
            $affordable_purchases[] = $card->canAfford($gold);
        }

        $args = array(
            "gold" => $gold,
            "affordable" => $affordable_purchases
        );
        return $args;
    }

    function buyTokens($fromCard, $fromSection)
    {
        $player_id = $this->getActivePlayerId();
        $player_name = $this->getActivePlayerName();

        $tokens_purchased = array();

        //get tokens purchased 
        $card_from_db = $this->market_cards->getCard($fromCard);
        $market_card = new \Classes\Market($card_from_db);

        $tokens_purchased = $this->tokens->getTokensInLocation($fromCard, "market", $fromSection, true);
        $cost = $market_card->cost[$fromSection];

        //create Action
        $action = new PurchaseTokenActionCommand($player_id, $player_name, $tokens_purchased, $cost);
        \Helpers\ActionManager::saveAction($action);
        $handlers = [
            "tokens" => $this->tokens,
        ];
        $action->setHandlers($handlers);
        $notifier = new \Helpers\ActionNotifier($player_id);
 
        $action->do($notifier);
        $this->gamestate->nextState("tokenBought");
    }




    function argOpenSlots(){
        $args = [];
        
        $active_player = $this->getActivePlayerId();

        $args["player_id"] = $active_player;
        $dungeon = new Dungeon($active_player);
    
        $args["slots"] = $dungeon->getAllOpenSlots();

        return $args;


    }
}

////////////////////////////////////////////////////////////////
////////////  ACTION CLASSES FOR IMRPOVEMENT PHASE  ////////////
////////////////////////////////////////////////////////////////

class PurchaseTokenActionCommand extends \Helpers\ActionCommand
{
    private $player_name;
    private $tokens_purchased;
    private $cost;
    protected $tokens;


    public function __construct($player_id, $player_name, $tokens_purchased, $cost)
    {
        $this->player_id = $player_id;
        $this->player_name = $player_name;
        $this->tokens_purchased = $tokens_purchased;
        $this->cost = $cost;
    }

    public function do($notifier)
    {
        //assign token to staging area of player
        $this->tokens->moveTokensToLocation(array_keys($this->tokens_purchased), $this->player_id, "player");
        //take gold from player
        \Helpers\Players::payGold($this->player_id, $this->cost);

        $notifier->notifyPlayerAndOthers(
            "tokens_purchased",
            ["private" => clienttranslate('${you} paid ${gold} gold to purchase tokens.'), 
            "public" => clienttranslate('${player_name} paid ${gold} gold to purchase tokens.')],
            array(
                "player_id" => $this->player_id,
                "player_name" => $this->player_name,
                "gold" => $this->cost,
                "tokens" => $this->tokens_purchased
            )
        );



    }

    public function undo($notifier){

        //move back tokens to market card
        foreach ($this->tokens_purchased as $token){
            $origin = $token["token_location"];
            $origin_slot = $token["token_location_slot"];
            $this->tokens->moveTokensToLocation($token["token_id"],$origin,"market", $origin_slot);
        }
        //return gold to player
        \Helpers\Players::gainGold($this->player_id, $this->cost);

        $notifier->notifyPlayerAndOthers(
            "tokens_returned",
            clienttranslate('${player_name} returned token(s).'),
            array(
                "player_id" => $this->player_id,
                "player_name" => $this->player_name,
                "gold" => $this->cost,
                "tokens" => $this->tokens_purchased
            )
        );

    }
    public function committ(){}
    public function reload($notifier = null){}
}
