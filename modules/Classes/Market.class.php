<?php
namespace Classes;

class Market extends \APP_DbObject
{

    private $top = array();
    private $bottom = array();
    private $card_id;
    public $cost = array();

    public function __construct($card_id = null)
    {

        if (is_null($card_id)) return;

        $sql = "SELECT card_id, top_cost, bottom_cost, token_top_1, token_top_2, token_top_3, token_bottom_1, token_bottom_2 FROM market WHERE card_id = $card_id";
        $card = $this->getCollectionFromDB($sql);
        $card = $card[$card_id];

        $this->card_id = $card["card_id"];

        $this->cost["top"] = $card["top_cost"];
        $this->top["token1"] = $card["token_top_1"];
        $this->top["token2"] = $card["token_top_2"];
        $this->top["token3"] = $card["token_top_3"];
        $this->cost["bottom"] = $card["bottom_cost"];
        $this->bottom["token1"] = $card["token_bottom_1"];
        $this->bottom["token2"] = $card["token_bottom_2"];

    }



    //this function returns card_id and if the player can afford to buy top or bottom token with the given $money
    public function canAfford($money)
    {
        $card = array();

        $card["top"] = ($money >= $this->cost["top"]);
        $card["bottom"] = ($money >= $this->cost["bottom"]);
    }

    //function to return the tokens ids assigned to this card (if any) and their position;
    public function getTokenShapes()
    {
        if (!isset($this->card_id)) return array();
        
        $tokens = array();


        if (is_null($this->top["token2"])) {
            $tokens[1][0] = $this->top["token1"];
        } else {
            foreach ($this->top as $index => $shape) {
                if (!is_null($shape)) $tokens[1][$index]  = $shape;
            }
        }
        if (is_null($this->bottom["token2"])) {
            $tokens[2][0] = $this->top["token1"];
        } else {
            foreach ($this->top as $index => $shape) {
                if (!is_null($shape)) $tokens[1][$index]  = $shape;
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

            $sql = "SELECT card_id, card_location_arg FROM $table WHERE card_location  = $this->card_id";
            $token = $this->getCollectionFromDB($sql, true);

            foreach ($token as $token_id => $token_location) {
                $tokens[$token_id][$token_location] = $shape;
            }
        }

        return $tokens;
    }
}
