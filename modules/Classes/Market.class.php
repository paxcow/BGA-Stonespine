<?php
namespace Classes;

class Market extends \APP_DbObject
{

    public $top = array();
    public $bottom = array();
    public $cost = array();
    public $id;

    public function __construct($card){

        if (is_null($card)) return;

        foreach ($card as $key => $value){
            $this->$key = $value;
        }

        $sql = "SELECT top_cost, bottom_cost, token_top_1, token_top_2, token_top_3, token_bottom_1, token_bottom_2 FROM market WHERE card_id = {$card["id"]}";
        $extra_info= $this->getObjectFromDB($sql);

        $this->id = $card["id"];
        $this->cost["top"] = $extra_info["top_cost"];
        $this->cost["bottom"] = $extra_info["bottom_cost"];
        $this->top[1] = $extra_info["token_top_1"];
        $this->top[2] = $extra_info["token_top_2"];
        $this->top[3] = $extra_info["token_top_3"];
        $this->bottom[1] = $extra_info["token_bottom_1"];
        $this->bottom[2] = $extra_info["token_bottom_2"];

    }



    //this function returns card_id and if the player can afford to buy top or bottom token with the given $money
    public function canAfford($money)
    {
        $card = array();
        $card["id"] = $this->id;
        $card["top"] = (!is_null($this->top[1]))? ($money >= $this->cost["top"]) : false;  //if there are tokens on the card, there is at least one on the first position
        $card["bottom"] = (!is_null($this->bottom[1]))? ($money >= $this->cost["bottom"]) : false;
        return $card;
    }

    //function to return the tokens ids assigned to this card (if any) and their position;
    public function getTokenShapes()
    {
        if (!isset($this->id)) return array();
        
        $tokens = array();

        if (is_null($this->top[2])) {
            $tokens[1][0] = $this->top[1];
        } else {
            foreach ($this->top as $index => $shape) {
                if (!is_null($shape)) $tokens[1][$index]  = $shape;
            }
        }
        if (is_null($this->bottom[2])) {
            $tokens[2][0] = $this->bottom[1];
        } else {
            foreach ($this->bottom as $index => $shape) {
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

            $sql = "SELECT * FROM $table WHERE card_location  = '$this->id'";
            $token = $this->getObjectListFromDB($sql);

            foreach ($token as $token_id => $token_location) {
                $tokens[$token_id][$token_location] = $shape;
            }
        }

        return $tokens;
    }
}
