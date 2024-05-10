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
 * stonespinearchitects.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in stonespinearchitects_stonespinearchitects.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */

require_once(APP_BASE_PATH . "view/common/game.view.php");


class view_stonespinearchitects_stonespinearchitects extends game_view
{
    protected function getGameName()
    {
        // Used for translations and stuff. Please do not modify.
        return "stonespinearchitects";
    }

    function build_page($viewArgs)
    {
        // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count($players);

        /*********** Place your code below:  ************/

        global $g_user;
        $current_player_id = $g_user->get_id();


        $this->tpl['MY_HAND'] = self::_("My Hand");



        $this->page->begin_block("stonespinearchitects_stonespinearchitects", "OTHER_PLAYERS");

        foreach ($players as $player_id => $player) {
            if ($player_id == $current_player_id) continue;

            $this->page->insert_block(
                "OTHER_PLAYERS",
                array(
                    "PLAYER_ID" => $player_id,
                    "PLAYER_NAME" => $player["player_name"],
                )
            );
        }
    }
}
