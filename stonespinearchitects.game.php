<?php

/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * stonespinearchitects.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */

use Classes\Dungeon;
use Classes\Hand;
use Helpers\ActionManager;

require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');
require_once('modules/utilities.php');
require_once('modules/Helpers/Players.inc.php');
require_once('modules/Classes/Market.class.php');
require_once('modules/Classes/Blueprint.class.php');
require_once('modules/Classes/Chamber.class.php');
require_once('modules/Classes/Dungeon.class.php');
require_once('modules/States/setup.traits.inc.php');
require_once('modules/States/construction.traits.inc.php');
require_once('modules/States/improvement.traits.inc.php');
require_once('modules/States/endgame.traits.inc.php');
require_once('modules/Classes/Hand.class.php');
require_once('modules/Helpers/dbManager.inc.php');
require_once('modules/Helpers/actionManager.inc.php');

//require_once('modules/deck.game.php');
//require_once('modules/table.game.php');



class StonespineArchitects extends Table
{
    use \Helpers\Undo;
    use \States\SetupGame;
    use \States\Construction;


    public $chamber_cards = null;
    public $challenge_cards = null;
    public $goal_cards = null;
    public $blueprint_cards = null;
    public $market_cards = null;
    public $oval_tokens = null;
    public $square_tokens = null;
    public $circle_tokens = null;






    function __construct()
    {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();
        self::trace("*****************START*******************");
        \Helpers\ActionManager::init();
        \Helpers\ActionNotifier::setGame($this);
        \Classes\Hand::setGame($this);
        \Classes\Dungeon::setGame($this);


        self::initGameStateLabels(array(

            "CHAMBERS_TO_PLAY" => 10,
            "YEARS_TO_PLAY" => 11,
            "CURRENT_YEAR" => 12
            //    "my_first_global_variable" => 10,
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ));

        //initialize decks of cards
        $this->initializeDecks();
    }

    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "stonespinearchitects";
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array())
    {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            $color = array_shift($default_colors);
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "')";
        }
        $sql .= implode(",", $values);
        self::DbQuery($sql);
        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();


        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );

        self::setGameStateInitialValue('CHAMBERS_TO_PLAY', 4);
        self::setGameStateInitialValue('YEARS_TO_PLAY', 4);
        self::setGameStateInitialValue('CURRENT_YEAR', 0);

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)

        // setup the initial game situation here
        $this->populateDecks();
        $this->initialGameSetup();



        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }




    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {

        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
        $players = $this->loadPlayersBasicInfos();

        $dungeon = [];
        $hand = [];
        $clientState= [];

        //rebuild game situation
        //load database into volatile structure

        foreach ($players as $player_id => $player) {
            $clientState[$player_id] = false;
            $dungeon[$player_id] = new Dungeon($player_id);
            $this_player = $player_id == $current_player_id;
            $hand[$player_id] = new Hand($player_id, $this_player);
        }

        $handlers = ['dungeon' => &$dungeon[$current_player_id], 'hand' => &$hand[$current_player_id], 'state' => &$clientState[$current_player_id]];

        

        //reload actions from current player - other players' pending actions are not visible to this player
        \Helpers\ActionManager::reloadAllActions($current_player_id, $handlers);

        //build the info to send to client
        $result = array();

        // Get information about players
        $sql = "SELECT player_id id, player_score score, gold, prev_player, next_player, priority, new_priority FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        //send client state
        $result['client_state'] = $clientState[$current_player_id]; 

        //players dungeons
        foreach ($dungeon as $player_id => $pl_dungeon) {
            $result['dungeon'][$player_id] = $pl_dungeon->getDungeon();
        }

        //tokens out
        $sql = "SELECT * FROM oval_token WHERE card_location <> 'deck'";
        $result['tokens']['oval'] = self::getCollectionFromDb($sql);
        $sql = "SELECT * FROM square_token WHERE card_location <> 'deck'";
        $result['tokens']['square'] = self::getCollectionFromDb($sql);
        $sql = "SELECT * FROM circle_token WHERE card_location <> 'deck'";
        $result['tokens']['circle'] = self::getCollectionFromDb($sql);

        //cards on the table
        $sql = "SELECT card_id FROM challenge WHERE card_location = 'table'";
        $result['table']['challenge'] = array_keys(self::getCollectionFromDb($sql));

        $sql = "SELECT * FROM market WHERE card_location <> 'deck'";
        $result['table']['market'] = self::getCollectionFromDb($sql);

        $sql = "SELECT card_id FROM goal WHERE card_location <> 'deck'";
        $result['table']['goal'] = self::getUniqueValueFromDB($sql);

        //players' hands]

        foreach ($players as $player_id => $player) {

            $result['hand'][$player_id]["chamber"] = $hand[$player_id]->getHand();

            if ($player_id == $current_player_id) {
                //current players hand
                $sql = "SELECT card_id FROM challenge WHERE card_location_arg = '$current_player_id'";
                $result['hand'][$player_id]['challenge'] = array_keys(self::getCollectionFromDb($sql));
                $sql = "SELECT card_id FROM blueprint WHERE card_location_arg = '$current_player_id'";
                $result['hand'][$player_id]['blueprint'] = array_keys(self::getCollectionFromDb($sql));
            }
        }
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        $players = $this->loadPlayersBasicInfos();
        $chambers_played = 0;
        $num_players = count($players);

        foreach ($players as $player_id => $player) {
            $dungeon = new \Classes\Dungeon($player_id);
            $chambers_played += $dungeon->getDungeonSize();
        }

        $total_chambers = 16 * $num_players;
        $full_dungeon = 16;

        $progression = $chambers_played / $total_chambers * 100;

        return $progression;
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */



    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in stonespinearchitects.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */


    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

    function argOpenDungeonSlots()
    {
        $open_slots = array();

        $players = $this->loadPlayersBasicInfos();

        foreach ($players as $player_id => $player) {
            $dungeon = new \Classes\Dungeon($player_id);
            $open_slots[$player_id] = $dungeon->getDungeonOpenSlots();
        }

        return $open_slots;
    }

    function argPassCards()
    {

        $args = array();

        $year = $this->getGameStateValue("CURRENT_YEAR");

        if ($year % 2 == 0) {

            $destination = "counterclockwise";
            $pass_order = $this->getPrevPlayerTable();
        } else {

            $destination = "clockwise";
            $pass_order = $this->getNextPlayerTable();
        }

        $args["destination"] = $destination;
        $args["pass_order"] = $pass_order;

        return $args;
    }

    function argPurchaseableTokens()
    {
        $active_player = $this->getActivePlayerId();
        $gold = \Helpers\Players::getGold($active_player);
        $market_cards = $this->market_cards->getCardsInLocation("table");
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////



    function st2plDiscardCard()
    {

        $players  = $this->loadPlayersBasicInfos();
        if (count($players) > 2) {

            $this->gamestate->nextState("passCardsToNextPlayer");
        } else {
            //special code for 2 player
            $this->gamestate->nextState("passCardsToNextPlayer");
        }
    }

    function stPassCards()
    {

        $args = $this->argPassCards();

        $players = $this->loadPlayersBasicInfos();

        foreach ($players as $player_id => $player) {

            //next player in table order    
            $next_player_id = $args["pass_order"][$player_id];

            //pass cards to next player
            $this->chamber_cards->moveAllCardsInLocation("hand", "hand", $player_id, $next_player_id);

            //notify the new player (the one who got the cards, not the current player!)

            $notif_args = array();
            $notif_args["source"] = $player_id;
            $notif_args["cards"] = array_keys($this->chamber_cards->getCardsInLocation("hand", $next_player_id));

            $this->notifyPlayer($next_player_id, "cardsPassed", totranslate("You received 5 new cards from " . $player["player_name"]), $notif_args);
        }

        $this->gamestate->nextState("");
    }

    function st2plDrawCard()
    {

        $players  = $this->loadPlayersBasicInfos();
        if (count($players) == 2) {

            //special code for 2 player
            //foreach

        }
        $this->gamestate->nextState("");
    }

    function stImprovementPhase()
    {

        $transition = (\Helpers\Players::haveGold() > 0) ? "activatePlayer" : "everyonePassed";

        $this->gamestate->nextState($transition);
    }

    function stActivateRichestPlayer()
    {

        $next_player = \Helpers\Players::getRichest();
        $this->gamestate->changeActivePlayer($next_player);
        $this->gamestate->nextState("");
    }


    public function translate(string $text)
    {
        return self::_($text);
    }

    static function inner_dump(string $message, mixed $object)
    {
        return self::dump($message, $object);
    }

    static function inner_trace(mixed $object)
    {
        return self::trace($object);
    }
    /////////////////////////////////////////////////////////////////////////////
    //////////// Zombie
    ////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $active_player)
    {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: " . $statename);
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version)
    {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //


    }
}
