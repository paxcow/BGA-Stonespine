<?php

namespace States;

require_once(__DIR__ . '/../Helpers/actionManager.inc.php');

trait Construction
{


    ///// Setup turn
    function stPrepareYear()
    {

        $players = array_keys($this->loadPlayersBasicInfos());

        foreach ($players as $player_id) {
            $this->chamber_cards->pickcards(5, "deck", $player_id);
        }
        $year = self::getGameStateValue('CURRENT_YEAR');

        $this->gamestate->nextState("");
    }


    //// Hub state: tracks game end condition
    function stConstructionPhase()
    {

        $transition = "nextChamber";

        $year_end = $this->getGameStateValue('CHAMBERS_TO_PLAY') == 0;
        if ($year_end) $transition = "yearEnd";

        $this->gamestate->nextState($transition);
    }


    //// Args to manage Play Card state

    function argPlayCard()
    {


        $players = array_keys($this->loadPlayersBasicInfos());

        //chamber cards in players' hands, as list of card_id

        $chambers = array();

        foreach ($players as $player_id) {

            $chambers[$player_id] = array_keys($this->chamber_cards->getCardsInLocation("hand", $player_id));
        }

        //challenge cards in players' hands, as a list of card_id

        $challenges = array();

        foreach ($players as $player_id) {

            $challenges[$player_id] = array_keys($this->challenge_cards->getCardsInLocation("hand", $player_id));
        }

        //dungeon slots the player can play card to as slot => (row,col)

        $open_slots = array();

        foreach ($players as $player_id) {

            $dungeon = new \Classes\Dungeon($player_id);
            $open_slots[$player_id] = $dungeon->getDungeonOpenSlots();
        }

        //compose the arg array with private info and send
        $args = array();
        foreach ($players as $player_id) {

            $args["_private"][$player_id] = array(

                "chambers" => $chambers[$player_id],
                "challenges" => $challenges[$player_id],
                "slots" => $open_slots[$player_id]

            );
        };
        return $args;
    }

    function placeChamberCard($card, $row, $col)
    {
        $this->checkAction("placeChamberCard");
        $cardName = $this->chamber_data[$card]["name"];
        $player_id = $this->getCurrentPlayerId();
        $player_name = $this->loadPlayersBasicInfos()[$player_id]["player_name"];
        $player_nbr = $this->getPlayersNumber();

        //rebuild game situation from db
        $client_state = false;
        $hand = new \Classes\Hand($player_id);
        $dungeon = new \Classes\Dungeon($player_id);
        $handlers = ['state' => &$client_state, 'hand' => &$hand, 'dungeon' => &$dungeon];


        //reload pending actions to update volatile game situation
        \Helpers\ActionManager::reloadAllActions($player_id, $handlers);

        //add new action to the action_table and update the volatile situation
        $action = new PlayCardActionCommand($player_id, $player_name,  $player_nbr,  $card, $cardName, $row, $col);
        self::dump("*****************ACTION*********************", $action);
        \Helpers\ActionManager::saveAction($action);
        $action->setHandlers($handlers);
        $notifier = new \Helpers\ActionNotifier($player_id);
        $action->do($notifier);

        if ($this->getPlayersNumber() != 2) {
            $this->gamestate->setPlayerNonMultiactive($player_id, "");
        }
    }

    function discardChamberCard($card)
    {
        $this->checkAction("discardChamberCard");

        $player_id = $this->getCurrentPlayerId();
        $player_name = $this->loadPlayersBasicInfos()[$player_id]["player_name"];
        $cardName = $this->chamber_data[$card]["name"];

        $client_state = false;
        $hand = new \Classes\Hand($player_id);
        $dungeon = new \Classes\Dungeon($player_id);

        $handlers = ['state' => &$client_state, 'hand' => &$hand, 'dungeon' => &$dungeon];


        \Helpers\ActionManager::reloadAllActions($player_id, $handlers);

        $action = new DiscardCardActionCommand($player_id, $player_name, $card, $cardName);
        \Helpers\ActionManager::saveAction($action);
        $action->setHandlers($handlers);
        $notifier = new \Helpers\ActionNotifier($player_id);
        $action->do($notifier);

        $this->gamestate->setPlayerNonMultiactive($player_id, "");
    }



    function stRevealCards()
    {
        \Helpers\ActionManager::committAllActions();
    }
}



////////////////////////////////////////////////////////////////
////////////  ACTION CLASSES FOR CONSTRUCTION PHASE ////////////
////////////////////////////////////////////////////////////////


class PlayCardActionCommand extends \Helpers\ActionCommand
{

    private $player_name;
    private $targetRow;
    private $targetCol;
    private $chamber;
    private $chamberName;
    private $player_nbr;
    protected $dungeon;
    protected $hand;
    protected $state;

    public function __construct($player_id, $player_name, $player_nbr, $chamber, $chamberName, $row, $col)
    {
        parent::__construct($player_id);
        $this->player_name = $player_name;
        $this->player_nbr = $player_nbr;
        $this->chamber = $chamber;
        $this->chamberName = $chamberName;
        $this->targetRow = $row;
        $this->targetCol = $col;
    }

    public function do($notifier)
    {
        $this->dungeon->addChamber($this->chamber, $this->targetRow, $this->targetCol);
        $this->hand->remove($this->chamber);

        if ($this->player_nbr == 2) {
            $this->state = true;
        }

        $notifier->notify(
            "card_placed",
            clienttranslate('You have placed ${card_name} in your dungeon'),
            array(
                "player_id" => $this->player_id,
                "card_id" => $this->chamber,
                "card_name" => $this->chamberName,
                "position" => $this->targetRow . $this->targetCol
            )
        );
    }
    public function undo($notifier)
    {
        $this->dungeon->removeChamber($this->chamber);
        $this->hand->add($this->chamber);
        if($this->player_nbr == 2){
            $this->state = false;
        }
        $notifier->notify(
            "undo_card_placed",
            clienttranslate('${card_name} is back in your hand'),
            array(
                "player_id" => $this->player_id,
                "card_id" => $this->chamber,
                "card_name" => $this->chamberName,
            )
        );

        \Helpers\ActionManager::removeAction($this->action_id);
    }
    public function reload($notifier = null)
    {
        $this->dungeon->addChamber($this->chamber, $this->targetRow, $this->targetCol);
        $this->hand->remove($this->chamber);
        $this->state = true;
    }
    public function committ($notifier = null, $notifyAll = true)
    {
        $this->dungeon->addChamber($this->chamber, $this->targetRow, $this->targetCol, true);
        $this->hand->manager->playCard($this->chamber);
        \Helpers\ActionManager::removeAction($this->action_id);

        if ($notifier) {
            if ($notifyAll) {
                $notifier->notifyAll(
                    "card_placed_all",
                    clienttranslate('${player_name} placed ${card_name} in his dungeon'),
                    array(
                        "player_id" => $this->player_id,
                        "player_name" => $this->player_name,
                        "card_id" => $this->chamber,
                        "card_name" => $this->chamberName,
                        "position" => $this->targetRow & $this->targetCol
                    )
                );
            }
        }
    }
}
class DiscardCardActionCommand extends \Helpers\ActionCommand
{

    private $player_name;
    private $chamber;
    private $chamberName;
    protected $hand;
    protected $dungeon;
    protected $state;

    public function __construct($player_id, $player_name, $chamber, $chamberName)
    {
        parent::__construct($player_id);
        $this->player_name = $player_name;
        $this->chamber = $chamber;
        $this->chamberName = $chamberName;
    }

    public function setHand(\Classes\Hand &$hand)
    {
        $this->hand = &$hand;
    }
    public function do($notifier)
    {
        $this->hand->remove($this->chamber);

        $notifier->notify(
            "card_discarded",
            clienttranslate('You have discarded ${card_name}'),
            array(
                "player_id" => $this->player_id,
                "card_id" => $this->chamber,
                "card_name" => $this->chamberName,
            )
        );
    }
    public function undo($notifier)
    {
        $this->hand->add($this->chamber);

        $notifier->notify(
            "undo_card_discarded",
            clienttranslate('${card_name} is back in your hand'),
            array(
                "player_id" => $this->player_id,
                "card_id" => $this->chamber,
                "card_name" => $this->chamberName,
            )
        );

        \Helpers\ActionManager::removeAction($this->action_id);
    }
    public function reload($notifier = null)
    {
        $this->hand->remove($this->chamber);
    }
    public function committ($notifier = null, $notifyAll = true)
    {
        $this->hand->manager->playCard($this->chamber);
        \Helpers\ActionManager::removeAction($this->action_id);
        if ($notifier) {
            if ($notifyAll) {
                $notifier->notifyAll(
                    "card_discarded_all",
                    clienttranslate('${player_name} discarded ${card_name}'),
                    array(
                        "player_id" => $this->player_id,
                        "player_name" => $this->player_name,
                        "card_id" => $this->chamber,
                        "card_name" => $this->chamberName,
                    )
                );
            }
        }
    }
}
