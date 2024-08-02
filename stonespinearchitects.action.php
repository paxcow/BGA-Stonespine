<?php

/**
 *------
 * BGA framework: Gregory Isabelli & Emmanuel Colin & BoardGameArena
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * stonespinearchitects.action.php
 *
 * StonespineArchitects main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/stonespinearchitects/stonespinearchitects/myAction.html", ...)
 *
 */


class action_stonespinearchitects extends APP_GameAction
{
    // Constructor: please do not modify
    public function __default()
    {
        if (self::isArg('notifwindow')) {
            $this->view = "common_notifwindow";
            $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
        } else {
            $this->view = "stonespinearchitects_stonespinearchitects";
            self::trace("Complete reinitialization of board game");
        }
    }

    // TODO: defines your action entry points there


    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

    public function placeChamberCard()
    {
        self::setAjaxMode();

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $card = self::getArg("card", AT_posint, true);
        $col = self::getArg("col", AT_posint, true);
        $row = self::getArg("row", AT_posint, true);


        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        self::trace("*********  CALLING ACTION PLACE IN CHAMBER ***************");
        $this->game->placeChamberCard($card, $row, $col);


        self::ajaxResponse();
    }


    public function discardChamberCard()
    {
        self::setAjaxMode();

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $card = self::getArg("card", AT_posint, true);


        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        self::trace("*********  CALLING ACTION DISCARDCARD ***************");
        $this->game->discardChamberCard($card);


        self::ajaxResponse();
    }




    public function undo()
    {
        self::trace("*********  UNDO!! ***************");
        self::setAjaxMode();
        print_r("undo!");

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $unpass = self::getArg("unpass", AT_bool, false, false);
        $steps = self::getArg("steps", AT_int,false,1);
        $changeStateAfter = self::getArg("state", AT_alphanum,false,false);

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->undo($unpass, $steps, $changeStateAfter);


        self::ajaxResponse();
    }

    public function buyTokens(){
        self::setAjaxMode();

        $card_id = self::getArg("id",AT_alphanum,true);
        $section = self::getArg("section",AT_alphanum,true);
        
        $this->game->buyTokens($card_id,$section);

        self::ajaxResponse();
    }
}
