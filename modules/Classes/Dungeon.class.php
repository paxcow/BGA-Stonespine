<?php

namespace Classes;

require_once("Chamber.class.php");

class Dungeon extends \APP_DbObject
{
    private $player_id;
    private $chambers;
    private $dungeon_elements;
    private $entry;
    private $exit;

    static private $game;
    public static function setGame($game){
        self::$game = $game;
    }

    public function __construct($player_id = null)
    {
        if (!is_null($player_id)) {
            $this->player_id = $player_id;
            $this->getDungeonFromDB($player_id);
        } else {
            $this->chambers = array();
        }
    }

    // gets the dungeon array from DB then recursively create Chamber objects in a linear array. Start and End doorway are stored in the Start and End properties    

    private function getDungeonFromDB($player_id)
    {

        $SQL = "SELECT * FROM dungeon WHERE `player_id` = $player_id";
        $this->dungeon_elements = $this->getCollectionFromDB($SQL);

        foreach ($this->dungeon_elements as $id => $element) {

            if ($element["chamber_id"] <> 9999) {

                $this->addChamber($element["chamber_id"], $element["row"], $element["column"]);
            } else {

                //doorways are not real chambers, but we manually add bottom and top connections for pathfinding

                if ($element["row"] == 0) {

                    $this->entry = ["row" => $element["row"], "col" => $element["column"]];
                    $this->chambers[$element["row"]][$element["column"]] = new Chamber();
                    $this->chambers[$element["row"]][$element["column"]]->bottom = true;
                } else {

                    $this->exit = ["row" => $element["row"], "col" => $element["column"]];
                    $this->chambers[$element["row"]][$element["column"]] = new Chamber();
                    $this->chambers[$element["row"]][$element["column"]]->top = true;
                }
            }
        }
    }

    // Getters
    public function getDungeon(){
        return $this->chambers;
    }


    public function getLastRow(){
        return max(array_keys($this->chambers));
    }


    public function getDungeonSize()
    {
        return (count($this->chambers) > 2) ? count($this->chambers) - 2 : null; // don't count entrance and exit doors
    }

    public function getDungeonOpenSlots()
    {
        $open_slots = array();
        $columns = [1, 2, 3, 4];

        $last_row = max(0, max(array_keys($this->chambers)));
        if ($last_row != 0) {
            $open_columns = array_diff($columns, array_keys($this->chambers[$last_row]));
        } else {
            $open_columns = $columns;
        }
        $is_full = count($open_columns) == 0;

        $open_slots["row"] = max($last_row, 1) + ($is_full ? 1 : 0);  //row 0 is reserved for doorways only, so we start from 1      
        $open_slots["col"] = array_values($open_columns);

        return $open_slots;
    }

    public function getAllOpenSlots()
    {
        $open_slots = array();
        foreach ($this->chambers as $r => $row ){
            if($r == 0 || $r == 5) continue;
            foreach ($row as $c => $chamber){
                $open_central_quadrants = $chamber->getOpenElements();
                $open_passages = $chamber->getOpenPassages();
                $open_slots[$chamber->id]["quadrant"] = $open_central_quadrants;
                $open_slots[$chamber->id]["passage"] = $open_passages;
            }
        }


        return $open_slots;
    }

    public function getIncome($notifier){
        $gold = 0;

        //create an array to store the lowest row occupied for each column
        $lowest_row = array_fill(1,4,-1);

        //in each column, find the card lower in the column
        foreach($this->chambers as $r => $row){
            foreach($row as $c => $chamber){
                $lowest_row[$c] = max($lowest_row[$c], $row);
        }}
        
        //then cycle throuhg all the cards, get gold from icons in the chamber + gold for the card in the last row

        foreach($this->chambers as $r => $row){
            foreach($row as $c => $chamber){
                if ($r<1) continue;
                $new_gold = $chamber->getGold($r == $lowest_row[$c]);
                $notifier->notifyAllPlayers("animate_gold_received","",[
                    "player_id" => $this->player_id,
                    "gold" => $new_gold,
                    "card_id" => $chamber->id,
                ]);
                $gold += $new_gold;
            }
        } 
        \Helpers\Players::gainGold($this->player_id,$gold);
        $notifier -> notifyAllPlayers("gold_received",clienttranslate('${player_name} receives ${gold} gold.'),[
            "player_name" => $notifier->getPlayerNameById($this->player_id),
            "player_id" => $this->player_id,
            "gold" => $gold,
        ]);
    }

    // Setters

    public function addChamber($chamber_id, $row, $col, $update_db = false)
    {
        if (isset($this->chambers[$row][$col])) {
            throw new \BgaUserException(self::$game->translate("WARNING: This position in the Dugneon is already occupied."));
        }
        $this->chambers[$row][$col] = new Chamber(($chamber_id != 9999) ? $chamber_id : null);
        if ($update_db) {
            $data = [$this->player_id, $row, $col, $chamber_id];
            $data = array_map(function ($val) {
                return formatForSQLQuery($val, false);
            }, $data);
            $data = implode(", ", $data);

            $sql = "INSERT INTO dungeon (`player_id`,`row`,`column`,`chamber_id`) VALUES (" . $data . ");";
            self::dump("SQL query: ", $sql);
            $this->DbQuery($sql);
        }
    }

    public function findChamber($chamber_id)
    {
        foreach ($this->chambers as $row => $row_data) {
            foreach ($row_data as $col => $chamber) {
                if ($chamber->id == $chamber_id) {
                    $position = ["row" => $row, "col" => $col];
                    return $position;
                }
            }
        }
        return null;
    }

    public function removeChamber($chamber_id, $update_db = false)
    {
        $position = $this->findChamber($chamber_id);
        if (!$position) {
            throw new \BgaUserException(self::$game->translate("WARNING: this Chamber is not in your Dungeon"));
        } else {
            unset($this->chambers[$position["row"]][$position["col"]]);
            if ($update_db) {
                $sql = "DELETE FROM dungeon WHERE `player_id` = $this->player_id AND `chamber_id`=$chamber_id;";
                self::dump("SQL query: ", $sql);
                $this->DbQuery($sql);
            }
        }
    }

    /////////////
    // REMOVED //
    /////////////
    // utilities to convert row,col to index and viceversa. Row ranges from 0 to 5, 
    // with 0 being the upper dungeon frame and 5 the lower dungeon frame for the door positioning.
    // column ranges from 1 to 4

    // private function encodeDungeonPosition($row, $col)
    // {
    //     return $row * 4 + ($col - 1);
    // }

    // private function decodeDungeonPosition($index)
    // {
    //     $position = array();
    //     $position['row'] = intdiv($index, 4);
    //     $position['col'] = $index % 4 + 1;
    //     return $position;
    // }

    //////////////////////////
    //pathfinding algorithms//
    //////////////////////////

    public function isConnectedToEntry($chamber_position, $entry)
    {
        $visited = array_fill(0, 6, array_fill(0, 4, false)); // initialize stack to keep track of visited chambers
        return $this->dfs($chamber_position["row"], $chamber_position["col"], $entry["row"], $entry["col"], $visited);
    }

    public function isConnectedToExit($chamber_position, $exit)
    {
        $visited = array_fill(0, 6, array_fill(0, 4, false)); // initialize stack to keep track of visited chambers
        return $this->dfs($chamber_position["row"], $chamber_position["col"], $exit["row"], $exit["col"], $visited);
    }


    private function dfs($row, $col, $end_row, $end_col, &$visited)
    {


        //check if the current position has already been visited,or if we are outside of the boundaries, or if the current position is empty
        if ($visited[$row][$col] || $row < 0 || $row > 5 || $col < 1 || $col > 4 || !isset($this->chambers[$row][$col])) {
            return false;
        }

        //mark the tile as visited
        $visited[$row][$col] = true;

        //check if we reached the target tile
        if ($row == $end_row && $col == $end_col) return true;

        //check connections from this chamber and recursively search from connected chambers
        $chamber_current = $this->chambers[$row][$col];
        $chamber_above = $this->chambers[$row - 1][$col];
        $chamber_below = $this->chambers[$row + 1][$col];
        $chamber_left = $this->chambers[$row][$col - 1];
        $chamber_right = $this->chambers[$row][$col + 1];

        //check above: current tile has connection up, there is a row above current row, there is a tile in the space above current tile, that tile has a connection down
        if ($chamber_current->top && $row > 0 && $chamber_above != null && $chamber_above->bottom) {
            if ($this->dfs($row - 1, $col, $end_row, $end_col, $visited)) return true;
        }
        //below
        if ($chamber_current->bottom && $row < 5 && $chamber_below != null && $chamber_below->top) {
            if ($this->dfs($row + 1, $col, $end_row, $end_col, $visited)) return true;
        }
        //left
        if ($chamber_current->left && $col > 1 && $chamber_left != null && $chamber_left->right) {
            if ($this->dfs($row, $col - 1, $end_row, $end_col, $visited)) return true;
        }
        //right
        if ($chamber_current->right && $col < 4 && $chamber_right != null && $chamber_right->left) {
            if ($this->dfs($row, $col + 1, $end_row, $end_col, $visited)) return true;
        }

        //no more connections to test, end chamber unreachable from here
        return false;
    }
}
