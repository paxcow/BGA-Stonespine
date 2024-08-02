<?php

namespace States;


trait SetupGame
{
    ///////////////////////////////////
    /// Create card and token decks ///
    ///////////////////////////////////
    function initializeDecks()
    {
        //init decks
        //TODO: Jael cards for Solo play
        $this->chamber_cards = self::getNew("module.common.deck");
        $this->chamber_cards->init("chamber");

        $this->market_cards = self::getNew("module.common.deck");
        $this->market_cards->init("market");

        $this->challenge_cards = self::getNew("module.common.deck");
        $this->challenge_cards->init("challenge");

        $this->goal_cards = self::getNew("module.common.deck");
        $this->goal_cards->init("goal");

        $this->blueprint_cards = self::getNew("module.common.deck");
        $this->blueprint_cards->init("blueprint");

        $this->tokens = new \Classes\TokenManager;
    }
    function populateDecks()
    {
        //populate token reserve
        $this->tokens->initTokenTable($this->token_data);
        
        //create decks

        //chamber cards
        $cards = array();

        foreach ($this->chamber_data as $id => $card) {
            $cards[] = array("type" => "chamber", "type_arg" => $id, "nbr" => 1);
        }
        $this->chamber_cards->createCards($cards);
        $this->chamber_cards->shuffle('deck');



        foreach ($this->chamber_data as $id => $card) {

            array_splice($card, 0, 1);

            $card = array_map("formatForSQLQuery", $card);
            $sql_part = array();

            foreach ($card as $column => $value) {
                $sql_part[] = "`$column` = " . $value;
            }

            $sql = "UPDATE chamber SET " . implode(", ", $sql_part) . " WHERE card_type_arg = $id";
            $this->DbQuery($sql);
        }

        //market cards
        $cards = array();
        foreach ($this->market_data as $id => $card) {
            $cards[] = array("type" => "market", "type_arg" => $id, "nbr" => 1);
        }
        $this->market_cards->createCards($cards);
        $this->market_cards->shuffle('deck');

        foreach ($this->market_data as $id => $card) {
            $card = array_map("formatForSQLQuery", $card);
            $sql_parts = array();

            foreach ($card as $column => $value) {
                $sql_parts[] = "`$column` = " . $value;
            }

            $sql = "UPDATE market SET " . implode(", ", $sql_parts) . " WHERE card_type_arg = $id";
            $this->DbQuery($sql);
        }

        //blueprint cards
        $cards = array();
        foreach ($this->blueprint_data as $id => $card) {
            $cards[] = array("type" => "blueprint", "type_arg" => $id, "nbr" => 1);
        }

        $this->blueprint_cards->createCards($cards);
        $this->blueprint_cards->shuffle('deck');

        foreach ($this->blueprint_data as $id => $scoring) {
            $scoring_json = json_encode($scoring);

            $scoring_json = formatForSQLQuery($scoring_json);

            $sql = "UPDATE blueprint SET `scoring` = $scoring_json WHERE `card_type_arg` = $id";
            $this->DbQuery($sql);
        }

        //challenge cards
        $cards = array();
        $this->challenge_data = range(0, 29);
        foreach ($this->challenge_data as $id => $card) {
            $cards[] = array("type" => "challenge", "type_arg" => $id, "nbr" => 1);
        }


        $this->challenge_cards->createCards($cards);
        $this->challenge_cards->shuffle('deck');


        //goal cards
        $cards = array();
        $this->goal_data = range(0, 7);
        foreach ($this->goal_data as $id => $card) {
            $cards[] = array("type" => "goal", "type_arg" => $id, "nbr" => 1);
        }
        $this->goal_cards->createCards($cards);
        $this->goal_cards->shuffle('deck');
    }

    //////////////////////////////////////////////////////////////////
    /// Setup table starting conditions, initial market cards, etc ///
    //////////////////////////////////////////////////////////////////
    function initialGameSetup()
    {


        $players = $this->loadPlayersBasicInfos();


        //init player info
        \Helpers\Players::initPriority();
        \Helpers\Players::initGold();

        //draw Goal card
        $this->goal_cards->pickCardForLocation("deck", "board");

        //distribute blueprints cards
        foreach ($players as $player_id => $player) {
            $this->blueprint_cards->pickCardForLocation("deck", "hand", $player_id);
        }

        //set the entrance doorway marker in each dungeon
        foreach ($players as $player_id => $player) {

            //find blueprint card of player
            $blueprint_id = array_keys($this->blueprint_cards->getCardsInLocation("hand", $player_id))[0];
            //get the blueprint details
            $blueprint = new \Classes\Blueprint($blueprint_id);

            //set the dungeon entry - it is treated as a chamber with id 9999 in row 0
            $dungeon = new \Classes\Dungeon($player_id);

            $dungeon->addChamber(9999, 0, $blueprint->getEntryCol(), true);
        }
    }
}
