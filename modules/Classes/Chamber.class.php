<?php
namespace Classes;
require_once(dirname(__FILE__,2). "/utilities.php");

class Chamber extends \APP_DbObject
{
    public $id;
    public $top;
    public $bottom;
    public $left;
    public $right;
    public $quadrant1;
    public $quadrant2;
    public $quadrant3;
    public $quadrant4;
    public $name;
    public $type;
    public $type_arg;
    public $gold;

    public function __construct($chamber_id = null)
    {

        if ($chamber_id != null) {
            $this->getChamberById($chamber_id);
        }
    }

    public function getChamberById($chamber_id)
    {
        $sql = "SELECT * FROM chamber WHERE `card_id` = $chamber_id";
        $cardFromDB = self::getObjectFromDB($sql);

        $this->id = $chamber_id;
        $this->type = $cardFromDB["card_type"];
        $this->type_arg = $cardFromDB["card_type_arg"];
        $this->top  = $cardFromDB["door_top"];
        $this->bottom  = $cardFromDB["door_bottom"];
        $this->left  = $cardFromDB["door_left"];
        $this->right  = $cardFromDB["door_right"];

        $this->quadrant1  = $cardFromDB["element_1"];
        $this->quadrant2  = $cardFromDB["element_2"];
        $this->quadrant3  = $cardFromDB["element_3"];
        $this->quadrant4  = $cardFromDB["element_4"];

        $this->name = $cardFromDB["chamber_name"];
        $this->gold = $cardFromDB["gold_value"];
    }

    public function addElement($quadrant, $element = null){
        $this->{"quadrant".$quadrant} = $element;
        $this->saveChamberToDb();
    }

    public function saveChamberToDb(){
        if (is_null($this->id)){throw new \BgaSystemException("Cannot save chamber card without id");
        }else{
            $fields = array("door_top","door_bottom","door_left","door_right","element_1","element_2","element_3","element_4");
            $data = array($this->top,$this->bottom,$this->left,$this->right,$this->quadrant1,$this->quadrant2,$this->quadrant3,$this->quadrant4);
            $data = array_map("formatFORSQLQuery",$data);
            $sql_parts = array();

            foreach($fields as $num => $field){
                $sql_parts[] = "`$field` = ".$data[$num];
            }
            $sql = "UPDATE chamber SET ".implode(", ",$sql_parts)."WHERE `card_id` = $this->id";
            $this->DbQuery($sql);
        }
    }
}
