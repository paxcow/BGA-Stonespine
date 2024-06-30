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
 * states.inc.php
 *
 * StonespineArchitects game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!
if (!defined("GAME_SETUP")){
    define('GAME_SETUP',1);
    define('PREPARE_YEAR',2);
    define('CONSTRUCTION_PHASE',3);
    define('PLAYERS_SELECT_CARD',4);
    define('REVEAL_CARDS',5);
//    define('_2PL_DISCARD_CARD',5);
    define('PASS_CARDS',6);
    define('_2PL_DRAW_CARD',7);
    //define('IS_END_OF_YEAR',8); /included in CONSTRUCTION_PHASE
    define('IMPROVEMENT_PHASE',9);
    define('ACTIVATE_NEXT_PLAYER',10);
    define('PLAYER_TOKEN_OR_PASS',11);
    define('PLAYER_PLACE_TOKEN',12);
    define('PLAYER_PASSED',13);
    define('PLAYER_SELECT_CHALLENGE',14);
    //define('ALL_PLAYERS_PASSED',18); / included in ACTIVATE_NEXT_PLAYER
    define('PREPARE_CLEANUP_PHASE',15);
    define('CLEANUP',16);
    define('END_GAME_SCORING',17);
    define('END_GAME',99);
 
}

 
$machinestates = array(

    // The initial state. Please do not modify.
    GAME_SETUP => array(
        "name" => "gameSetup",
        "description" => "",
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => PREPARE_YEAR )
    ),
    

    PREPARE_YEAR => array(
    		"name" => "prepareYear",
    		"description" => clienttranslate('Dealing each player 5 Chamber cards'),
    		"type" => "game",
    		"action" => "stPrepareYear",
    		"transitions" => array( "" => CONSTRUCTION_PHASE)
    ),
    
    CONSTRUCTION_PHASE => array(
        "name" => "constructionPhase",
        "description" => clienttranslate('Construction phase'),
        "type" => "game",
        "action" => "stConstructionPhase",
        "transitions" => array( "nextChamber" => PLAYERS_SELECT_CARD, "yearEnd" => IMPROVEMENT_PHASE)
    ),

    PLAYERS_SELECT_CARD => array(
        "name" => "playCard",
        "description" => clienttranslate('Waiting for other players to make their choice...'),
        "descriptionmyturn" =>  clienttranslate('${you} must choose a Chamber card and place it in your dungeon'),
        "type" => "multipleactiveplayer",
        "action" => "stMakeEveryoneActive",
        "args" => "argPlayCard",
        "possibleactions" => array("placeChamberCard","discardChamberCard"),
        "transitions" => array( "" => REVEAL_CARDS)
    ),

    REVEAL_CARDS => array(
        "name" => "revealCards",
        "description" => clienttranslate('Revealing cards played'),
        "type" => "game",
        "action" => "stRevealCards",
        "transitions" => array( "" => PASS_CARDS)
    ),


    PASS_CARDS => array(
        "name" => "passCards",
        "description" => clienttranslate('Passing cards in ${direction_label} direction'),
        "type" => "game",
        "action" => "stPassCards",
        "args" => "argPassCards",
        "transitions" => array("" => CONSTRUCTION_PHASE, "drawCards" => _2PL_DRAW_CARD)
    ),

    _2PL_DRAW_CARD => array(
        "name" => "2plDrawCard",
        "description" => clienttranslate('Drawing an extra Chamber card'),
        "type" => "game",
        "action" => "st2plDrawCard",
        "transitions" => array("" => CONSTRUCTION_PHASE) 
    ),

    IMPROVEMENT_PHASE => array(
        "name" => "improvementPhase",
        "description" => clienttranslate('Improvement phase'),
        "type" => "game",
        "action" => "stImprovementPhase",
        "transitions" => array("activatePlayer" => ACTIVATE_NEXT_PLAYER, "everyonePassed" => PREPARE_CLEANUP_PHASE) 
    ),

    ACTIVATE_NEXT_PLAYER => array(
        "name" => "activateRichestPlayer",
        "description" => "",
        "type" => "game",
        "action" => "stActivateRichestPlayer",
        "transitions" => array("" => PLAYER_TOKEN_OR_PASS) 
    ),

    PLAYER_TOKEN_OR_PASS => array(
        "name" => "playerTokenOrPass",
        "description" => clienttranslate('${you} must choose to buy a Token from the Market or Pass'),
        "type" => "activeplayer",
        "args" => "argPurchaseableTokens",
        "possibleactions" => array("buyToken","cancelToken","pass"), 
        "transitions" => array("tokenBought" => PLAYER_PLACE_TOKEN, "tokenCanceled" => PLAYER_TOKEN_OR_PASS, "playerPassed" => PLAYER_PASSED) 
    ),

    PLAYER_PLACE_TOKEN => array(
        "name" => "playerPlaceToken",
        "description" => clienttranslate('$(you) must place the token in your dungeon'),
        "type" => "game",
        "action" => "stSpendGold",
        "transitions" => array("" => IMPROVEMENT_PHASE) 
    ),

    PLAYER_PASSED => array(
        "name" => "playerPassed",
        "description" => "",
        "type" => "game",
        "action" => "stPlayerPassed",
        "transitions" => array("" => PLAYER_SELECT_CHALLENGE) 
    ),

    PLAYER_SELECT_CHALLENGE => array(
        "name" => "playerSelectChallenge",
        "description" => clienttranslate('${you} must select one of the available Challenge'),
        "type" => "multipleplayeractive",
        "action" => "stPlayerSelectChallenge",
        "possibleactions" => array("selectChallenge", "cancelChallenge"),
        "transitions" => array("challengeCanceled" => PLAYER_SELECT_CHALLENGE,"challengeSelected" => ACTIVATE_NEXT_PLAYER) 
    ),

    PREPARE_CLEANUP_PHASE => array(
        "name" => "prepareCleanUp",
        "description" => "",
        "type" => "game",
        "action" => "stPrepareCleanUp",
        "transitions" => array("cleanupReady" => CLEANUP, "finalYear" => END_GAME_SCORING) 
    ),

    CLEANUP => array(
        "name" => "Cleanup",
        "description" => "Cleanup",
        "type" => "game",
        "action" => "stCleanUp",
        "transitions" => array("" => PREPARE_YEAR) 
    ),

    END_GAME_SCORING => array(
        "name" => "endGameScoring",
        "description" => "Calculating final score",
        "type" => "game",
        "action" => "stFinalScoring",
        "transitions" => array("" => END_GAME) 
    ),

    /*
    Examples:
    
    2 => array(
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,   
        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
    ),
    
    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard", "pass" ),
        "transitions" => array( "playCard" => 2, "pass" => 2 )
    ), 

*/    
   
    // Final state.
    // Please do not modify (and do not overload action/args methods).
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);



