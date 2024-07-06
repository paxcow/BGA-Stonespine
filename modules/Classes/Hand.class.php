<?php

namespace Classes;



class Hand
//This class uses the DECK module to create a volatile, in-memory copy of the players hand, for the sake of do-undo.

{
    private $player_id;
    public $manager;
    private $hand;
    private $thisPlayer;

    private static $game;
    public static function setGame($game)
    {
        self::$game = $game;
    }


    public function __construct($player_id, $thisPlayer = true)
    {
        $this->player_id = $player_id;
        $this->manager = self::$game->chamber_cards;
        $this->thisPlayer = $thisPlayer;
        $this->init();
    }

    public function init()
    {
        $this->hand = array_keys($this->manager->getPlayerHand($this->player_id));
    }

    public function add($card_id)
    {

        if (array_search($card_id, $this->hand) === true) {
            throw new \BgaUserException(self::$game->translate("Warning: This card is already in hand"));
        } else {
            $this->hand[] = $card_id;
        }

    }

    public function remove($card_id)
    {

        $index = array_search($card_id, $this->hand);
        if ($index !== false) {
            unset($this->hand[$index]);
            $this->hand = array_values($this->hand);
        } else {
            throw new \BgaUserException(self::$game->translate("Warning: This card is not in your hand"));
        }

    }

    public function getHand(): array
    {
        $hand_object = array();
        foreach ($this->hand as $card) {
            $hand_object[] = array(
                "id" => $card,
                "type" => "chamber"
            );
        }

        return $hand_object;
    }

    public function getFullHand(): array
    {
        $hand_object = array();
        foreach ($this->hand as $card) {
            $hand_object[] = $this->getFullCard($card);
        }
        return $hand_object;

    }

    public function getFullCard($card_id)
    {

        if (array_search($card_id, $this->hand) === false) {
            throw new \BgaUserException(self::$game->translate("Warning: This card is not in your hand"));
        } else {
            return $this->manager->getCard($card_id);
        }
    }
}
