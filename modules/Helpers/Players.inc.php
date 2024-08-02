<?php
namespace Helpers;
/*
 * Stonespine Architects implementation : © Andrea "Paxcow" Vitagliano <andrea.vitagliano@gmail.com>
  * 
  * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
  * See http://en.boardgamearena.com/#!doc/Studio for more information.
  * 
  *
  * Helper functions for players
*/

class Players extends \APP_DbObject{

    private $gold;
    private $priority;
    public $player_id;


    //player setup

    
    //set initial gold and priority
    public static function initPriority(){
        $sql = "UPDATE player SET `priority` = `player_no`";
        self::DbQuery($sql);    
    }

    public static function initGold(){
        $sql = "UPDATE player SET `gold` = CASE
                    WHEN `priority` IN (2,3) THEN 1
                    WHEN `priority` IN (4,5) THEN 2
                END
                WHERE `priority` IN (2,3,4,5)";
        self::DbQuery($sql);    
    }


    //Priority helpers

  
    public static function setPriority($player_id, $priority){
        $sql = "UPDATE player SET `priority` = $priority where `player_id` = $player_id";
        self::DbQuery($sql);
    }

    public static function getPriority($player_id, $all = null){
        $sql = "SELECT `player_id`,`priority` FROM player";
        if (isset($all)) $sql.="WHERE `player_id` = $player_id";
        return self::getCollectionFromDB($sql);
    }

    public static function setNewPriority($player_id){
        $sql = "SELECT MAX(`priority`) FROM player";
        $new_priority =  self::getUniqueValueFromDB($sql) + 1;
        $sql = "UPDATE player SET 'new_priority` = $new_priority WHERE `player_id`=$player_id";
        self::DbQuery($sql);
    }

    public static function resetNewPriority(){
        $sql = "UPDATE player SET `new_priority` = 0";
        self::DbQuery($sql);
    }

    //compare at least two players and returns the player with the highest priority
    public static function tieBreaker($player1_id, $player2_id, $player3_id = null, $player4_id = null, $player5_id = null){
        
        //minimum two player_id needed for a comparison
        $ids = [$player1_id, $player2_id];

        //optionally up to 5 players part of the comparison
        if ($player3_id != null) $ids[] = $player3_id;
        if ($player4_id != null) $ids[] = $player4_id;
        if ($player5_id != null) $ids[] = $player5_id;

        $ids = implode(",",$ids);

        $sql = "SELECT `player_id` FROM player WHERE `player_id` IN ($ids) ORDER BY `priority` ASC LIMIT 1";
        return self::getUniqueValueFromDB($sql);
    }

    //gold helpers

    public static function getRichest($ties = false	){
        $sql = "SELECT `player_id` FROM player ORDER BY `gold` DESC, `priority` ASC LIMIT 1"; 
        return self::getUniqueValueFromDB($sql);
    }

    public static function getGold($player_id){
        $sql = "SELECT `gold` FROM player WHERE `player_id` = $player_id";
        return self::getUniqueValueFromDB($sql);
    }

    public static function gainGold($player_id, $gold = 1){
        if ($gold<0) return false;
        $sql = "UPDATE player SET `gold` = gold + $gold WHERE `player_id` = $player_id";
        return self::DbQuery($sql);
    }
    
    public static function payGold($player_id, $price = 0){

        $sql = "SELECT `gold` FROM player WHERE `player_id` = $player_id";
        $gold = self::getUniqueValueFromDB($sql);

        if ($price<0 || $gold < $price) return false;

        $sql = "UPDATE player SET `gold` = gold - $gold WHERE `player_id` = $player_id";
        return self::DbQuery($sql);
    }

    public static function haveGold(){
        $sql = "SELECT COUNT(player_id) FROM player WHERE `gold` > 0";
        return self::getUniqueValueFromDB($sql);
    }

}

?>