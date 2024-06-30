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

        $this->oval_tokens = self::getNew("module.common.deck");
        $this->oval_tokens->init("oval_token");

        $this->square_tokens = self::getNew("module.common.deck");
        $this->square_tokens->init("square_token");

        $this->circle_tokens = self::getNew("module.common.deck");
        $this->circle_tokens->init("circle_token");
    }
    function populateDecks()
    {


        //create decks

        //chamber cards
        $cards = array();

        foreach ($this->chamber_data as $id => $card) {
            $cards[] = array("type" => "chamber", "type_arg" => $id, "nbr" => 1);
        }
        $this->chamber_cards->createCards($cards);



        foreach ($this->chamber_data as $card) {

            array_splice($card,0,1);

            $card = array_map("formatForSQLQuery", $card);
            $sql_part = array();

            foreach ($card as $column => $value) {
                $sql_part[] = "`$column` = " . $value;
            }

            $sql = "UPDATE chamber SET " . implode(", ", $sql_part) . " WHERE card_type_arg = $id";
            $this->DbQuery($sql);
        }

        //market cards
        foreach ($this->market_data as $id => $card) {
            $cards[] = array("type" => "market", "type_arg" => $id, "nbr" => 1);
        }
        $this->market_cards->createCards($cards);

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

        foreach ($this->blueprint_data as $id => $card) {
            $cards[] = array("type" => "blueprint", "type_arg" => $id, "nbr" => 1);
        }

        $this->blueprint_cards->createCards($cards);

        foreach ($this->blueprint_data as $id => $scoring) {
            $scoring_json = json_encode($scoring);

            $scoring_json = formatForSQLQuery($scoring_json);

            $sql = "UPDATE blueprint SET `scoring` = $scoring_json WHERE `card_type_arg` = $id";
            $this->DbQuery($sql);
        }

        //challenge cards
        $this->challenge_data = range(1,30);
        foreach ($this->challenge_data as $id => $card) {
            $cards[] = array("type" => "challenge", "type_arg" => $id, "nbr" => 1);
        }

        $this->challenge_cards->createCards($cards);


        //goal cards
        $this->goal_data = range(1,8);
        foreach ($this->goal_data as $id => $card) {
            $cards[] = array("type" => "goal", "type_arg" => $id, "nbr" => 1);
        }
        $this->goal_cards->createCards($cards);

        //token piles

        foreach ($this->token_data as $shape => $tokens) {
            $cards = array();
            foreach ($tokens as $id => $token) {
                $cards[] = array("type" => $shape, "type_arg" => $token[0], "nbr" => $token[1]);
            }
            $this->{$shape . "_tokens"}->createCards($cards, "pile");
        }
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

        //draw Market cards
        switch (count($players)) {
            case 1:
            case 2:
            case 3:
                $nbr = 3;
                break;
            case 4:
                $nbr = 4;
                break;
            case 5:
                $nbr = 5;
                break;
        }
        $this->market_cards->pickCardsForLocation($nbr, "deck", "table");

        //draw Challenge cards
        switch (count($players)) {
            case 1:
            case 2:
                $nbr = 3;
                break;
            case 3:
                $nbr = 4;
                break;
            case 4:
                $nbr = 5;
                break;
            case 5:
                $nbr = 6;
                break;
        }
        $this->challenge_cards->pickCardsForLocation($nbr, "deck", "table");

        //populate market cards with tokens
        $market = array_keys($this->market_cards->getCardsInLocation("table"));
        foreach ($market as $index => $marketCard_id) {
            $card = new \Classes\Market($marketCard_id);
            $token_shapes = $card->getTokenShapes();
            foreach ($token_shapes as $panel => $shapes) {
                foreach ($shapes as $pos => $shape) {
                    if ($shape != null) $this->{$shape . "_tokens"}->pickCardForLocation("pile", $marketCard_id, (int)($panel . $pos));
                }
            }
        }

        //distribute blueprints cards
        foreach ($players as $player_id => $player) {
            $this->blueprint_cards->pickCardForLocation("deck", "player_board", $player_id);
        }

        //set the entrance doorway marker in each dungeon
        foreach ($players as $player_id => $player) {

            //find blueprint card of player
            $blueprint_id = array_keys($this->blueprint_cards->getCardsInLocation("player_board", $player_id))[0];
            //get the blueprint details
            $blueprint = new \Classes\Blueprint($blueprint_id);

            //set the dungeon entry - it is treated as a chamber with id 9999 in row 0
            $dungeon = new \Classes\Dungeon($player_id);

            $dungeon->addChamber(9999, 0, $blueprint->getEntryCol(), true);
        }
    }


    
}
