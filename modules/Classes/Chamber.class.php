<?php

namespace Classes;

require_once(dirname(__FILE__, 2) . "/utilities.php");

class Chamber extends \APP_DbObject
{
    public $id;
    public $name;
    public $type;
    public $type_arg;
    public $gold;
    public $quadrant;
    public $passage;
    public $door;
    public $direction;

    public function __construct($chamber_id = null)
    {
        if ($chamber_id != null) {
            $this->getChamberById($chamber_id);
        }
    }

    public function getChamberById($chamber_id)
    {
        $directions = ["top","bottom","left","right"];

        $sql = "SELECT * FROM chamber WHERE `card_id` = $chamber_id";
        $cardFromDB = self::getObjectFromDB($sql);
        $this->id = $chamber_id;
        $this->type = $cardFromDB["card_type"];
        $this->type_arg = $cardFromDB["card_type_arg"];

        foreach($directions as $direction){
            $this->door[$direction] = $cardFromDB["door_$direction"];
            $this->passage[$direction] = $cardFromDB["passage_$direction"];
            $this->direction[$direction] = $this->door[$direction] || $this->passage[$direction];
        }

        $this->quadrant[1]  = $cardFromDB["element_1"];
        $this->quadrant[2]  = $cardFromDB["element_2"];
        $this->quadrant[3]  = $cardFromDB["element_3"];
        $this->quadrant[4]  = $cardFromDB["element_4"];

        $this->name = $cardFromDB["chamber_name"];
        $this->gold = $cardFromDB["gold_value"];
    }

    public function addElement($quadrant, $element)
    {
        $this->quadrant[$quadrant] = $element;
        $this->saveChamberToDb();
    }

    public function addPassage($direction)
    {
        $this->passage[$direction] = true;
        $this->saveChamberToDb();
    }


    public function saveChamberToDb()
    {
        if (is_null($this->id)) {
            throw new \BgaSystemException("Cannot save chamber card without id");
        } else {
            $fields = array("element_1", "element_2", "element_3", "element_4", "passage_top", "passage_bottom", "passage_left", "passage_right");
            $data = array_values(array_merge($this->quadrant,$this->passage));
            $data = array_map("formatFORSQLQuery", $data);
            $sql_parts = array();

            foreach ($fields as $num => $field) {
                $sql_parts[] = "`$field` = " . $data[$num];
            }
            $sql = "UPDATE chamber SET " . implode(", ", $sql_parts) . "WHERE `card_id` = $this->id";
            $this->DbQuery($sql);
        }
    }

    public function getGold($last_row = false)
    {

        $gold = $this->gold;
        foreach ($this->quadrant as $symbol) {
            $gold += ($symbol == "treasure") ? 1 : 0;
        }

        return $gold;
    }

    public function getOpenElements()
    {
        $emptyQuadrants= array_keys(array_filter($this->quadrant, function ($q) {
            return $q == null;
        })); 
        return $emptyQuadrants; 
    }
    public function getOpenPassages()
    {   
        $emptyPassages= array_keys(array_filter($this->passage, function ($p) {
            return $p == false;
        })); 
        return $emptyPassages; 
    }
    public function getOpenDirections()
    {   
        $emptyDirections= array_keys(array_filter($this->direction, function ($d) {
            return $d == false;
        })); 
        return $emptyDirections; 
    }
}
