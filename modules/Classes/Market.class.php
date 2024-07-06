<?php
namespace Classes;

class Market extends \APP_DbObject
{

    private $top = array();
    private $bottom = array();
    public $card;
    public $cost = array();
    public $id;

    public function __construct(&$card)
    {

        if (is_null($card)) return;

        $this->card = &$card;

        $sql = "SELECT top_cost, bottom_cost, token_top_1, token_top_2, token_top_3, token_bottom_1, token_bottom_2 FROM market WHERE card_id = {$card["id"]}";
        $extra_info= $this->getObjectFromDB($sql);

        $this->id = $card["id"];
        $this->card["cost"]["top"] = $extra_info["top_cost"];
        $this->card["cost"]["bottom"] = $extra_info["bottom_cost"];
        $this->card["top"][1] = $extra_info["token_top_1"];
        $this->card["top"][2] = $extra_info["token_top_2"];
        $this->card["top"][3] = $extra_info["token_top_3"];
        $this->card["bottom"][1] = $extra_info["token_bottom_1"];
        $this->card["bottom"][2] = $extra_info["token_bottom_2"];

    }



    //this function returns card_id and if the player can afford to buy top or bottom token with the given $money
    public function canAfford($money)
    {
        $card = array();
        $card["id"] = $this->card["id"];
        $card["top"] = ($money >= $this->cost["top"]);
        $card["bottom"] = ($money >= $this->cost["bottom"]);
    }

    //function to return the tokens ids assigned to this card (if any) and their position;
    public function getTokenShapes()
    {
        if (!isset($this->card["id"])) return array();
        
        $tokens = array();

        if (is_null($this->card["top"][2])) {
            $tokens[1][0] = $this->card["top"][1];
        } else {
            foreach ($this->card["top"] as $index => $shape) {
                if (!is_null($shape)) $tokens[1][$index]  = $shape;
            }
        }
        if (is_null($this->card["bottom"][2])) {
            $tokens[2][0] = $this->card["bottom"][1];
        } else {
            foreach ($this->card["bottom"] as $index => $shape) {
                if (!is_null($shape)) $tokens[2][$index]  = $shape;
            }
        }

        return $tokens;
    }

    public function getTokenIds()
    {

        $tokens = array();
        $shapes = ["circle", "square", "oval"];

        foreach ($shapes as $shape) {
            $table = $shape . "_token";

            $sql = "SELECT * FROM $table WHERE card_location  = {$this->card["id"]}";
            $token = $this->getObjectListFromDB($sql);

            foreach ($token as $token_id => $token_location) {
                $tokens[$token_id][$token_location] = $shape;
            }
        }

        return $tokens;
    }
}
