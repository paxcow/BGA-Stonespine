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
 * material.inc.php
 *
 * StonespineArchitects game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/

$this->chamber_data = array(
  // SQL structure: id, type, door_top, door_bottom, door_left, door_right, element_1, element_2, element_3, element_4,name,gold,solo
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'blades'  , "element_2" => 'starneg' , "element_3" => null      , "element_4" => 'slime'   , "chamber_name" => 'Smokehouse'      , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'bear'    , "element_2" => null      , "element_3" => 'spike'   , "element_4" => null      , "chamber_name" => 'Training Room'   , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'pit'     , "element_2" => null      , "element_3" => null      , "element_4" => null      , "chamber_name" => 'Secret Path'     , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'kobold'  , "element_2" => null      , "element_3" => 'star1'   , "element_4" => null      , "chamber_name" => "Furrow"          , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => null      , "chamber_name" => 'Gnoll Hideout'   , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => null      , "element_2" => 'gnoll'   , "element_3" => null      , "element_4" => 'kobold'  , "chamber_name" => 'Cavern'          , "gold_value" => 1, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'blades'  , "element_2" => null      , "element_3" => 'bear'    , "element_4" => 'spike'   , "chamber_name" => 'Trap Storage'    , "gold_value" => 0, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'blades'  , "element_2" => null      , "element_3" => 'star1'   , "element_4" => 'slime'   , "chamber_name" => 'Corner Room'     , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'starneg' , "element_2" => 'ooze'    , "element_3" => null      , "element_4" => 'slime'   , "chamber_name" => 'Abandoned Mine'  , "gold_value" => 5, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'treasure', "element_2" => null      , "element_3" => 'fire'    , "element_4" => null      , "chamber_name" => 'Scorch Alley'    , "gold_value" => 1, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'kobold'  , "element_3" => 'spike'   , "element_4" => 'starneg' , "chamber_name" => 'Torment Room'    , "gold_value" => 5, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'star1'   , "element_3" => null      , "element_4" => 'slime'   , "chamber_name" => 'Crooked Path'    , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'slime'   , "element_2" => 'slime'   , "element_3" => null      , "element_4" => null      , "chamber_name" => 'Slime Trail'     , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'ooze'    , "element_2" => 'starneg' , "element_3" => 'ooze'    , "element_4" => 'slime'   , "chamber_name" => 'Cellar'          , "gold_value" => 4, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'goblin'  , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => null      , "chamber_name" => 'Derelict Site'   , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" => 'Ambush Room'     , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'goblin'  , "element_3" => null      , "element_4" => 'goblin'  , "chamber_name" => 'Tunnel'          , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'treasure', "element_2" => null      , "element_3" => 'ooze'    , "element_4" => 'pit'     , "chamber_name" => 'Supply Room'     , "gold_value" => 2, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'bear'    , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => 'spike'   , "chamber_name" => 'Armory'          , "gold_value" => 1, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'slime'   , "element_3" => null      , "element_4" => 'ooze'    , "chamber_name" => 'Burial Ground'   , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'kobold'  , "element_3" => null      , "element_4" => 'kobold'  , "chamber_name" => 'Kobold Quarters' , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'ooze'    , "element_2" => null      , "element_3" => 'star1'   , "element_4" => null      , "chamber_name" => 'Footpath'        , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'kobold'  , "element_3" => null      , "element_4" => 'kobold'  , "chamber_name" => 'Exercise Ward'   , "gold_value" => 2, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => null      , "element_3" => null      , "element_4" => 'treasure', "chamber_name" => 'Archive'         , "gold_value" => 1, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'treasure', "element_2" => 'treasure', "element_3" => null      , "element_4" => null      , "chamber_name" => 'Tomb'            , "gold_value" => 0, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => null      , "element_2" => 'ooze'    , "element_3" => null      , "element_4" => 'slime'   , "chamber_name" => 'Lair'            , "gold_value" => 4, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => null      , "element_2" => 'gnoll'   , "element_3" => null      , "element_4" => 'goblin'  , "chamber_name" => 'Watering Hole'   , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => null      , "chamber_name" => 'Passage'         , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => 'star1'   , "element_2" => null      , "element_3" => 'fire'    , "element_4" => null      , "chamber_name" => 'Furnace'         , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'fire'    , "element_2" => 'ooze'    , "element_3" => 'treasure', "element_4" => null      , "chamber_name" => 'Hearth'          , "gold_value" => 0, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => false, "element_1" => null      , "element_2" => 'star1'   , "element_3" => 'treasure', "element_4" => 'star1'   , "chamber_name" => 'Sanctuary'       , "gold_value" => 0, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => false, "element_1" => 'slime'   , "element_2" => 'pit'     , "element_3" => 'ooze'    , "element_4" => null      , "chamber_name" => 'Catacombs'       , "gold_value" => 5, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => false, "element_1" => null      , "element_2" => null      , "element_3" => 'kobold'  , "element_4" => null      , "chamber_name" => 'Alcove'          , "gold_value" => 5, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => false, "element_1" => 'treasure', "element_2" => 'treasure', "element_3" => null      , "element_4" => "treasure", "chamber_name" => 'Treasury'        , "gold_value" => 0, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => false, "element_1" => 'star1'   , "element_2" => 'star1'   , "element_3" => 'star1'   , "element_4" => null      , "chamber_name" => 'Ruins'           , "gold_value" => 1, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => null      , "element_2" => null      , "element_3" => 'star1'   , "element_4" => 'bear'    , "chamber_name" => 'Burrow'          , "gold_value" => 0, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => null      , "chamber_name" => 'Encampent'       , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => null      , "element_2" => null      , "element_3" => null      , "element_4" => 'ooze'    , "chamber_name" => 'Ossiary'         , "gold_value" => 5, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => 'treasure', "element_2" => null      , "element_3" => 'slime'   , "element_4" => null      , "chamber_name" => 'Coffers'         , "gold_value" => 1, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'ooze'    , "element_3" => 'pit'     , "element_4" => null      , "chamber_name" => 'Concourse'       , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'goblin'  , "element_3" => NULL      , "element_4" => 'star3'   , "chamber_name" => 'Arena'           , "gold_value" => 0, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'gnoll'   , "element_3" => NULL      , "element_4" => 'spike'   , "chamber_name" => 'Hollow'          , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" => 'Gathering Place' , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'slime'   , "element_3" => NULL      , "element_4" => 'slime'   , "chamber_name" => 'Graveyard'       , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'ooze'    , "element_3" => NULL      , "element_4" => 'ooze'    , "chamber_name" => 'Sludge Room'     , "gold_value" => 2, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => "fire"    , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => null      , "chamber_name" => 'Branch'          , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'treasure', "element_3" => NULL      , "element_4" => 'star1'   , "chamber_name" => 'Cache'           , "gold_value" => 0, "solo_rune" => 'red'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'starneg3', "element_4" => null      , "chamber_name" => 'Crossroads'      , "gold_value" => 8, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => null      , "element_3" => NULL      , "element_4" => 'gnoll'   , "chamber_name" => 'Intersection'    , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'cave', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'slime'   , "element_3" => NULL      , "element_4" => 'star1'   , "chamber_name" => 'Throughway'      , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'goblin'  , "element_2" => null      , "element_3" => 'blade'   , "element_4" => null      , "chamber_name" => 'Stockyard'       , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'slime'   , "element_2" => 'ooze'    , "element_3" => 'smile'   , "element_4" => 'ooze'    , "chamber_name" => 'Mudroom'         , "gold_value" => 2, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => null      , "element_2" => 'goblin'  , "element_3" => null      , "element_4" => 'gnoll'   , "chamber_name" => 'Assembly Room'   , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => null      , "chamber_name" => 'Vestibule'       , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'kobold'  , "element_2" => null      , "element_3" => 'star3'   , "element_4" => null      , "chamber_name" => 'Guardroom'       , "gold_value" => 0, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'goblin'  , "element_2" => null      , "element_3" => 'treasure', "element_4" => null      , "chamber_name" => 'Vault'           , "gold_value" => 1, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'ooze'    , "element_2" => null      , "element_3" => 'gnoll'   , "element_4" => null      , "chamber_name" => 'Trophy Room'     , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'ooze'    , "element_2" => null      , "element_3" => null      , "element_4" => 'ooze'    , "chamber_name" => 'Ooze Breach'     , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'ooze'    , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => null      , "chamber_name" => 'Mausoleum'       , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'kobold'  , "element_3" => null      , "element_4" => 'treasure', "chamber_name" => 'Conservatory'    , "gold_value" => 1, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => 'spike'   , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" => 'Garrison'        , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => 'treasure', "element_2" => null      , "element_3" => 'star1'   , "element_4" => null      , "chamber_name" => 'Keep'            , "gold_value" => 2, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'slime'   , "element_3" => null      , "element_4" => 'slime'   , "chamber_name" => 'Slime Incubator' , "gold_value" => 4, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => 'gnoll'   , "element_3" => null      , "element_4" => 'kobold'  , "chamber_name" =>  'Dormitory'      , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => null      , "element_3" => 'bear'    , "element_4" => 'starneg2', "chamber_name" =>  'Cell'           , "gold_value" => 7, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'ooze'    , "element_2" => null      , "element_3" => 'kobold'  , "element_4" => null      , "chamber_name" =>  'Sanctum'        , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => null      , "chamber_name" =>  'Alleyway'       , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => true , "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" =>  'Lounge'         , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => 'treasure', "element_2" => null      , "element_3" => 'fire'    , "element_4" => null      , "chamber_name" =>  'Breezeway'      , "gold_value" => 1, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => 'bear'    , "element_2" => null      , "element_3" => 'blade'   , "element_4" => null      , "chamber_name" =>  'Machine Room'   , "gold_value" => 1, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => null      , "element_2" => 'slime'   , "element_3" => null      , "element_4" => 'gnoll'   , "chamber_name" => 'Flagstone Hall'  , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => false, "element_1" => 'slime'   , "element_2" => null      , "element_3" => null      , "element_4" => 'ooze'    , "chamber_name" => 'Floodgate'       , "gold_value" => 4, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => false, "door_right" => false, "element_1" => null      , "element_2" => 'slime'   , "element_3" => null      , "element_4" => 'ooze'    , "chamber_name" => 'Crypt'           , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => 'gnoll'   , "element_2" => null      , "element_3" => 'star1'   , "element_4" => null      , "chamber_name" => 'Atrium'          , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => 'kobold'  , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" => 'Auditorium'      , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => null      , "element_2" => null      , "element_3" => 'pit'     , "element_4" => null      , "chamber_name" => 'Pit'             , "gold_value" => 4, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => false, "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => null      , "chamber_name" =>  'winding Chamber', "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => 'kobold'  , "element_2" => null      , "element_3" => 'kobold'  , "element_4" => null      , "chamber_name" =>  'Gatehouse'      , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => false, "door_right" => true , "element_1" => null      , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => 'star1'   , "chamber_name" =>  'Oratory'        , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'ooze'    , "element_3" => null      , "element_4" => 'starneg' , "chamber_name" =>   'Decrepit Hall' , "gold_value" => 5, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'slime'   , "element_3" => null      , "element_4" => 'ooze'    , "chamber_name" => 'Corridor'        , "gold_value" => 2, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'goblin'  , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" => 'Dwelling'        , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'star1'   , "element_2" => 'goblin'  , "element_3" => null      , "element_4" => null      , "chamber_name" => 'Larder'          , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'slime'   , "element_3" => null      , "element_4" => 'spike'   , "chamber_name" => 'Storeroom'       , "gold_value" => 3, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => null      , "element_3" => 'kobold'  , "element_4" => null      , "chamber_name" => 'Galley'          , "gold_value" => 5, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => false, "door_bottom" => false, "door_left" => true , "door_right" => true , "element_1" => 'ooze'    , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => null      , "chamber_name" => 'Sewers'          , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'treasure', "element_4" => null      , "chamber_name" => 'Foundry'         , "gold_value" => 1, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => 'goblin'  , "element_2" => null      , "element_3" => 'goblin'  , "element_4" => null      , "chamber_name" => 'Goblin Den'      , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'pit'     , "element_3" => null      , "element_4" => 'goblin'  , "chamber_name" => 'Warehouse'       , "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => false, "door_bottom" => true , "door_left" => true , "door_right" => true , "element_1" => null      , "element_2" => 'kobold'  , "element_3" => null      , "element_4" => 'slime'   , "chamber_name" => 'Antechamber'     , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => null      , "element_2" => 'gnoll'   , "element_3" => 'kobold'  , "element_4" => 'goblin'  , "chamber_name" => 'Barracks'        , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => null      , "element_2" => 'slime'   , "element_3" => 'ooze'    , "element_4" => 'goblin'  , "chamber_name" => 'Tainted Clearing', "gold_value" => 2, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => 'treasure', "chamber_name" => 'Closet'          , "gold_value" => 0, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => null      , "element_2" => 'star2'   , "element_3" => null      , "element_4" => 'gnoll'   , "chamber_name" => 'Pantry'          , "gold_value" => 3, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => null      , "element_2" => null      , "element_3" => null      , "element_4" => null      , "chamber_name" => 'Junction'        , "gold_value" => 6, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => null      , "element_2" => 'fire'    , "element_3" => null      , "element_4" => 'fire'    , "chamber_name" => 'Forge'           , "gold_value" => 4, "solo_rune" => 'blue'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'ooze'    , "element_4" => "starneg1", "chamber_name" => 'Crossing'        , "gold_value" => 3, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => null      , "element_2" => 'star1'   , "element_3" => null      , "element_4" => "kobold"  , "chamber_name" => 'Hallway'         , "gold_value" => 2, "solo_rune" => 'green'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => 'slime'   , "element_2" => 'ooze'    , "element_3" => 'slime'   , "element_4" => null      , "chamber_name" => 'Cesspool'        , "gold_value" => 1, "solo_rune" => 'red'),
array("type" => 'rock', "door_top" => true , "door_bottom" => true , "door_left" => true , "door_right" => 0    , "element_1" => 'slime'   , "element_2" => null      , "element_3" => 'slime'   , "element_4" => null      , "chamber_name" => 'Lost Chamber'    , "gold_value" => 4, "solo_rune" => 'red'),
);


$this->token_data = array(
  //shape,type,number
  "square" => array(
    1 => array('bear', 3),
    2 => array('blade', 3),
    3 => array('fire', 3),
    4 => array('pit', 3),
    5 => array('spike', 3),
  ),
  "circle" => array(
    1 => array('ooze', 6),
    2 => array('slime', 6),
    3 => array('star1', 7),
    4 => array('treasure', 6),
  ),
  "oval" => array(
    1 => array('gnoll', 4),
    2 => array('goblin', 4),
    3 => array('kobold', 4),
    4 => array('secret', 4),
  )
);

$this->token_types = array(
  "monsters" => ["gnoll", "goblin", "kobold", "ooze", "slime"],
  "traps" => ["bear", "blade", "fire", "pit", "spike"],
  "stars" => ["star1","star2","star3","starneg"]
);


$this->market_data = array(
  //cost_top, top_shape_1, top_shape_2, top_shape_3, cost_bottom, bottom_shape
array("top_cost" => 9 , "token_top_1" => 'square', "token_top_2" => 'square', "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 7 , "token_top_1" => 'circle', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 3, "token_bottom_1" => 'circle', "token_bottom_2" => null), 
array("top_cost" => 10, "token_top_1" => 'oval'  , "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'circle', "token_bottom_2" => null), 
array("top_cost" => 10, "token_top_1" => 'circle', "token_top_2" => 'circle', "token_top_3" => 'circle', "bottom_cost" => 5, "token_bottom_1" => 'square', "token_bottom_2" => null), 
array("top_cost" => 8 , "token_top_1" => 'square', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 7 , "token_top_1" => 'square', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 3, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 6 , "token_top_1" => 'circle', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 7 , "token_top_1" => 'circle', "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 9 , "token_top_1" => 'square', "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 5, "token_bottom_1" => 'circle', "token_bottom_2" => 'circle'), 
array("top_cost" => 8 , "token_top_1" => 'square', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 7 , "token_top_1" => 'circle', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'square', "token_bottom_2" => null), 
array("top_cost" => 9 , "token_top_1" => 'oval'  , "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 3, "token_bottom_1" => 'circle', "token_bottom_2" => null), 
array("top_cost" => 6 , "token_top_1" => 'circle', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 5, "token_bottom_1" => 'square', "token_bottom_2" => null), 
array("top_cost" => 5 , "token_top_1" => 'square', "token_top_2" => null    , "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 7 , "token_top_1" => 'square', "token_top_2" => 'circle', "token_top_3" => null    , "bottom_cost" => 5, "token_bottom_1" => 'square', "token_bottom_2" => null), 
array("top_cost" => 9 , "token_top_1" => 'square', "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'oval'  , "token_bottom_2" => null), 
array("top_cost" => 7 , "token_top_1" => 'circle', "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 4, "token_bottom_1" => 'square', "token_bottom_2" => null), 
array("top_cost" => 9 , "token_top_1" => 'oval'  , "token_top_2" => 'oval'  , "token_top_3" => null    , "bottom_cost" => 7, "token_bottom_1" => 'square', "token_bottom_2" => 'circle'), 
);

$this->market_layouts = array(
  //types of different layouts for market cards, for CSS purpose
  1 => "squaresquare",
  2 => "circlecircle",
  3 => "ovaloval",
  4 => "circlecirclecircle",
  5 => "squarecircle",
  6 => "circleoval",
  7 => "squareoval",
  8 => "square",
  9 => "oval",
  10 => "circle"
);

$this->blueprint_data = array(
  //col_start, row1 [col1 to col4]...row4 [col1 to col4],col_end
  array(
    "start" => 2,
    "row1" => array("col1" => null, "col2" => null, "col3" => 'treasure', "col4" => 'cave'),
    "row2" => array("col1" => "goblin", "col2" => "cave", "col3" => null, "col4" => null),
    "row3" => array("col1" => null, "col2" => null, "col3" => 'cave', "col4" => 'ooze'),
    "row4" => array("col1" => "cave", "col2" => "slime", "col3" => null, "col4" => null),
    "end" => 4
  ),
  array(
    "start" => 3,
    "row1" => array("col1" => "star", "col2" => "cave", "col3" => null, "col4" => null),
    "row2" => array("col1" => "cave", "col2" => null, "col3" => null, "col4" => "trap"),
    "row3" => array("col1" => null, "col2" => null, "col3" => 'goblin', "col4" => 'cave'),
    "row4" => array("col1" => null, "col2" => "treasure", "col3" => "cave", "col4" => null),
    "end" => 1
  ),
  array(
    "start" => 2,
    "row1" => array("col1" => null, "col2" => null, "col3" => "stone", "col4" => "star"),
    "row2" => array("col1" => "trap", "col2" => null, "col3" => null, "col4" => "stone"),
    "row3" => array("col1" => "stone", "col2" => "gnoll", "col3" => null, "col4" => null),
    "row4" => array("col1" => null, "col2" => "stone", "col3" => "kobold", "col4" => null),
    "end" => 4
  ),
  array(
    "start" => 3,
    "row1" => array("col1" => null, "col2" => "kobold", "col3" => null, "col4" => "stone"),
    "row2" => array("col1" => "goblin", "col2" => null, "col3" => "stone", "col4" => null),
    "row3" => array("col1" => null, "col2" => "stone", "col3" => "star", "col4" => null),
    "row4" => array("col1" => "stone", "col2" => null, "col3" => null, "col4" => "gnoll"),
    "end" => 2
  ),
  array(
    "start" => 2,
    "row1" => array("col1" => "stone", "col2" => null, "col3" => "ooze", "col4" => null),
    "row2" => array("col1" => null, "col2" => null, "col3" => "stone", "col4" => "treasure"),
    "row3" => array("col1" => "slime", "col2" => "stone", "col3" => null, "col4" => null),
    "row4" => array("col1" => null, "col2" => "gnoll", "col3" => null, "col4" => "stone"),
    "end" => 2
  ),
  array(
    "start" => 2,
    "row1" => array("col1" => "treasure", "col2" => null, "col3" => "cave", "col4" => null),
    "row2" => array("col1" => "cave", "col2" => null, "col3" => "slime", "col4" => null),
    "row3" => array("col1" => null, "col2" => "ooze", "col3" => null, "col4" => "cave"),
    "row4" => array("col1" => null, "col2" => "cave", "col3" => null, "col4" => "star"),
    "end" => 3
  ),
  array(
    "start" => 3,
    "row1" => array("col1" => null, "col2" => "stone", "col3" => null, "col4" => "trap"),
    "row2" => array("col1" => null, "col2" => null, "col3" => "slime", "col4" => "stone"),
    "row3" => array("col1" => "stone", "col2" => "kobold", "col3" => null, "col4" => null),
    "row4" => array("col1" => "ooze", "col2" => null, "col3" => "stone", "col4" => null),
    "end" => 2
  ),
  array(
    "start" => 2,
    "row1" => array("col1" => "cave", "col2" => null, "col3" => "kobold", "col4" => null),
    "row2" => array("col1" => "trap", "col2" => "cave", "col3" => null, "col4" => null),
    "row3" => array("col1" => null, "col2" => null, "col3" => "cave", "col4" => "goblin"),
    "row4" => array("col1" => null, "col2" => "gnoll", "col3" => null, "col4" => "cave"),
    "end" => 1
  ),

);

$this->direction = array(
  "cw" => clienttranslate("clockwise"),
  "ccw" => clienttranslate("counterclockwise")
);
