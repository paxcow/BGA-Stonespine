<?php
namespace Classes;

class Blueprint extends \APP_DbObject{

    private $start = 0;
    private $end = 0;
    private $row1 = array();
    private $row2 = array();
    private $row3 = array();
    private $row4 = array();


    public function __construct($blueprint_id){

        $scoring = array();
        $scoring_json = "";

        $sql = "SELECT scoring FROM blueprint WHERE card_id = $blueprint_id";
        $scoring_json = self::getUniqueValueFromDB($sql);

        $scoring = json_decode($scoring_json, true);

        foreach ($scoring as $param => $value){
                $this->{$param} = $value;  
        }

    }

    public function getEntryCol(){
        return $this->start;
    }

    public function getExitCol(){
        return $this->end;
    }
    public function getConditionForPosition($row, $col){
        return $this->{"row".$row}["col".$col];
    }

}

?>