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
        $this->hand = ($this->thisPlayer) ? $this->hand : count($this->hand);
    }

    public function add($card_id)
    {
        if ($this->thisPlayer) {
            if (array_search($card_id, $this->hand)) {
                throw new \BgaUserException(self::$game->translate("Warning: This card is already in hand"));
            } else {
                $this->hand[] = $card_id;
            }
        } else {
            $this->hand++;
        }
    }

    public function remove($card_id)
    {

        if ($this->thisPlayer) {
            $index = array_search($card_id, $this->hand);
            if ($index !== false) {
                unset($this->hand[$index]);
                $this->hand = array_values($this->hand);
            } else {
                throw new \BgaUserException(self::$game->translate("Warning: This card is not in your hand"));
            }
        } else {
            $this->hand = min(0, $this->hand--);
        }
    }

    public function getHand():array|int|null{
        return $this->hand ?? null;
    }
}
