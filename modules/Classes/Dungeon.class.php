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
    public static function setGame($game)
    {
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
    public function getDungeon()
    {
        return $this->chambers;
    }


    public function getLastRow()
    {
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
        foreach ($this->chambers as $r => $row) {
            if ($r == 0 || $r == 5) continue;
            foreach ($row as $c => $chamber) {
                $open_central_quadrants = $chamber->getOpenElements();
                $open_passages = $chamber->getOpenPassages();
                $open_slots[$chamber->id]["quadrant"] = $open_central_quadrants;
                $open_slots[$chamber->id]["passage"] = $open_passages;
            }
        }


        return $open_slots;
    }

    public function getIncome($notifier)
    {
        $gold = 0;

        //create an array to store the lowest row occupied for each column
        $lowest_row = array_fill(1, 4, -1);

        //in each column, find the card lower in the column
        foreach ($this->chambers as $r => $row) {
            foreach ($row as $c => $chamber) {
                $lowest_row[$c] = max($lowest_row[$c], $row);
            }
        }

        //then cycle throuhg all the cards, get gold from icons in the chamber + gold for the card in the last row

        foreach ($this->chambers as $r => $row) {
            foreach ($row as $c => $chamber) {
                if ($r < 1) continue;
                $new_gold = $chamber->getGold($r == $lowest_row[$c]);
                $notifier->notifyAllPlayers("animate_gold_received", "", [
                    "player_id" => $this->player_id,
                    "gold" => $new_gold,
                    "card_id" => $chamber->id,
                ]);
                $gold += $new_gold;
            }
        }
        \Helpers\Players::gainGold($this->player_id, $gold);
        $notifier->notifyAllPlayers("gold_received", clienttranslate('${player_name} receives ${gold} gold.'), [
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

    public function getPaths()
    {
        // Initialize the visited array to keep track of chambers we've already included in a path
        $visited = array_fill(1, 4, array_fill(1, 4, false)); // 4 rows (1 to 4) and 4 columns (1 to 4)

        // Initialize the array to hold all paths
        $paths = [];

        // Loop through each chamber in the dungeon to find paths
        for ($row = 1; $row <= 4; $row++) {
            for ($col = 1; $col <= 4; $col++) {
                if (isset($this->chambers[$row][$col]) && !$visited[$row][$col]) {
                    // Start a new path if the chamber is not visited
                    $path = [];
                    $this->dfsCollectPath($row, $col, $visited, $path);

                    if (!empty($path)) {
                        $paths[] = $path;
                    }
                }
            }
        }

        return $paths;
    }

    private function dfsCollectPath($row, $col, &$visited, &$path)
    {
        // Check if the current position has already been visited, or if we are outside the boundaries, or if the current position is empty
        if ($visited[$row][$col] || $row < 1 || $row > 4 || $col < 1 || $col > 4 || !isset($this->chambers[$row][$col])) {
            return;
        }

        // Mark the chamber as visited and add it to the current path
        $visited[$row][$col] = true;
        $path[$row][$col] = $this->chambers[$row][$col];

        // Define possible directions and their corresponding row/col changes
        $directions = [
            ['rowOffset' => -1, 'colOffset' => 0, 'currentDir' => 'top', 'oppositeDir' => 'bottom'],  // up
            ['rowOffset' => 1, 'colOffset' => 0, 'currentDir' => 'bottom', 'oppositeDir' => 'top'],  // down
            ['rowOffset' => 0, 'colOffset' => -1, 'currentDir' => 'left', 'oppositeDir' => 'right'],  // left
            ['rowOffset' => 0, 'colOffset' => 1, 'currentDir' => 'right', 'oppositeDir' => 'left'],  // right
        ];

        // Check each direction for connections and recursively collect chambers
        foreach ($directions as $dir) {
            $nextRow = $row + $dir['rowOffset'];
            $nextCol = $col + $dir['colOffset'];

            if ($this->isConnected($row, $col, $dir['currentDir'], $nextRow, $nextCol, $dir['oppositeDir'])) {
                $this->dfsCollectPath($nextRow, $nextCol, $visited, $path);
            }
        }
    }

    private function isConnected($row, $col, $direction, $nextRow, $nextCol, $oppositeDirection)
    {
        if (isset($this->chambers[$nextRow][$nextCol])) {
            $currentChamber = $this->chambers[$row][$col];
            $nextChamber = $this->chambers[$nextRow][$nextCol];

            return $currentChamber->direction[$direction] && $nextChamber->direction[$oppositeDirection];
        }

        return false;
    }

    public function isConnectedToEntry($path, $entry)
    {
        // The entry is always at row 0 and has a connection direction of 'bottom'
        $entryRow = 0;
        $entryCol = $entry['col'];

        // Check the chamber in row 1 directly below the entry
        if (isset($path[1][$entryCol])) {
            $chamber = $path[1][$entryCol];
            if ($this->isConnected($entryRow, $entryCol, 'bottom', $chamber->row, $chamber->col, 'top')) {
                return true;
            }
        }

        return false;
    }

    public function isConnectedToExit($path, $exit)
    {
        // The exit is always at row 5 and has a connection direction of 'top'
        $exitRow = 5;
        $exitCol = $exit['col'];

        // Check the chamber in row 4 directly above the exit
        if (isset($path[4][$exitCol])) {
            $chamber = $path[4][$exitCol];
            if ($this->isConnected($exitRow, $exitCol, 'top', $chamber->row, $chamber->col, 'bottom')) {
                return true;
            }
        }

        return false;
    }

    public function calculatePathLength($path)
    {
        $length = 0;

        // Iterate through each row in the path
        foreach ($path as $row) {
            // Count the number of chambers in the row and add to the total length
            $length += count($row);
        }

        return $length;
    }


    //////////////////////////
    ////Cluster algorithms////
    //////////////////////////

    public function getClusters()
    {
        // Initialize variables
        $visited = array_fill(1, 4, array_fill(1, 4, false)); // 4 rows (1 to 4) and 4 columns (1 to 4)
        $clusters = [];

        // Loop through chambers in rows 1 to 4
        for ($row = 1; $row <= 4; $row++) {
            for ($col = 1; $col <= 4; $col++) {
                if (isset($this->chambers[$row][$col]) && !$visited[$row][$col]) {
                    // Start a new cluster if not visited
                    $chamber = $this->chambers[$row][$col];
                    $cluster = $this->findCluster($row, $col, $chamber->type, $visited);
                    if (!empty($cluster)) {
                        $clusters[] = $cluster;
                    }
                }
            }
        }

        return $clusters;
    }

    private function findCluster($row, $col, $type, &$visited)
    {
        // Define the directions for orthogonal adjacency (top, bottom, left, right)
        $directions = [
            [-1, 0], // up
            [1, 0],  // down
            [0, -1], // left
            [0, 1],  // right
        ];

        // Initialize the stack for DFS and the cluster array
        $stack = [[$row, $col]];
        $cluster = array_fill(1, 4, array_fill(1, 4, null)); // Initialize cluster structure

        while (!empty($stack)) {
            list($r, $c) = array_pop($stack);

            // Skip if out of bounds (rows 1 to 4 and columns 1 to 4) or already visited
            if ($r < 1 || $r > 4 || $c < 1 || $c > 4 || $visited[$r][$c]) {
                continue;
            }

            // Skip if the chamber is not of the same type
            if (!isset($this->chambers[$r][$c]) || $this->chambers[$r][$c]->type !== $type) {
                continue;
            }

            // Mark the chamber as visited and add to the current cluster
            $visited[$r][$c] = true;
            $cluster[$r][$c] = clone $this->chambers[$r][$c]; // Copy the chamber

            // Check adjacent chambers
            foreach ($directions as $dir) {
                $newRow = $r + $dir[0];
                $newCol = $c + $dir[1];
                $stack[] = [$newRow, $newCol];
            }
        }

        // Remove empty rows/columns 
        $cluster = array_filter($cluster, fn($row) => array_filter($row, fn($chamber) => $chamber !== null));

        return $cluster;
    }

    public function calculateClusterSize($cluster)
    {
        // Initialize the cluster size
        $clusterSize = 0;

        // Iterate through the cluster to calculate the number of chambers
        foreach ($cluster as $row) {
            $clusterSize += count(array_filter($row, fn($chamber) => $chamber !== null));
        }

        return $clusterSize;
    }

    /////////////////////////////////////////////////////////////////////////////
    /////////////////////   POINT FUNCTIONS /////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////

    public function calculateCentralChamberPoints($element, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Determine if $element is a group type or an array of specific elements
        if (is_string($element) && isset($tokenTypes[$element])) {
            $elements = $tokenTypes[$element];  // It's a group type
        } elseif (is_array($element)) {
            $elements = $element;  // It's an array of specific elements
        } else {
            throw new \Exception("Invalid element or group type provided.");
        }

        // Initialize the total points
        $totalPoints = 0;

        // Define the central chambers' positions
        $centralChamberPositions = [
            ['row' => 2, 'col' => 2],
            ['row' => 2, 'col' => 3],
            ['row' => 3, 'col' => 2],
            ['row' => 3, 'col' => 3],
        ];

        // Loop through the central chambers and calculate the points
        foreach ($centralChamberPositions as $position) {
            $row = $position['row'];
            $col = $position['col'];

            if (isset($this->chambers[$row][$col])) {
                $chamber = $this->chambers[$row][$col];

                // Loop through the quadrants to calculate the total points
                for ($i = 1; $i <= 4; $i++) {
                    if (in_array($chamber->quadrant[$i], $elements)) {
                        $totalPoints += $points;
                    }
                }
            }
        }

        return $totalPoints;
    }

    public function calculatePointsFromNumberOfClusters($points)
    {
        // Get the list of clusters from the dungeon
        $clusters = $this->getClusters();

        // Calculate the total points based on the number of clusters
        $totalPoints = count($clusters) * $points;

        return $totalPoints;
    }

    public function calculatePointsFromLargestCluster($points)
    {
        // Get the list of clusters from the dungeon
        $clusters = $this->getClusters();

        // Initialize the maximum cluster size
        $maxClusterSize = 0;

        // Iterate through each cluster to find the largest one
        foreach ($clusters as $cluster) {
            // Use the helper function to calculate the size of the current cluster
            $clusterSize = $this->calculateClusterSize($cluster);

            // Update the maximum cluster size if the current one is larger
            $maxClusterSize = max($clusterSize, $maxClusterSize);
        }

        // Calculate the total points based on the largest cluster size
        $totalPoints = $maxClusterSize * $points;

        return $totalPoints;
    }

    public function calculateMaxColumnPoints($element, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Determine if $element is a group type or a single element
        if (is_string($element) && isset($tokenTypes[$element])) {
            $elements = $tokenTypes[$element];  // It's a group type
        } elseif (is_string($element)) {
            $elements = [$element];  // It's a single element
        } else {
            throw new \Exception("Invalid element or group type provided.");
        }

        // Initialize the maximum points
        $maxPoints = 0;

        // Iterate through each column (1 to 4)
        for ($col = 1; $col <= 4; $col++) {
            $elementCount = 0;

            // Iterate through each row (1 to 4) in the current column
            for ($row = 1; $row <= 4; $row++) {
                if (isset($this->chambers[$row][$col])) {
                    $chamber = $this->chambers[$row][$col];

                    // Count the occurrences of the specified element(s) in the chamber's quadrants
                    for ($i = 1; $i <= 4; $i++) {
                        if (in_array($chamber->quadrant[$i], $elements)) {
                            $elementCount++;
                        }
                    }
                }
            }

            // Calculate the points for this column and update maxPoints if it's the highest
            $columnPoints = $elementCount * $points;
            $maxPoints = max($maxPoints, $columnPoints);
        }

        return $maxPoints;
    }

    public function calculateMaxClusterPoints($element, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Determine if $element is a group type or a single element
        if (is_string($element) && isset($tokenTypes[$element])) {
            $elements = $tokenTypes[$element];  // It's a group type
        } elseif (is_string($element)) {
            $elements = [$element];  // It's a single element
        } else {
            throw new \Exception("Invalid element or group type provided.");
        }

        // Get the list of clusters from the dungeon
        $clusters = $this->getClusters();

        // Initialize the maximum points
        $maxPoints = 0;

        // Iterate through each cluster
        foreach ($clusters as $cluster) {
            $elementCount = 0;

            // Iterate through each chamber in the cluster
            foreach ($cluster as $row) {
                foreach ($row as $chamber) {
                    if ($chamber !== null) {
                        // Count the occurrences of the specified element(s) in the chamber's quadrants
                        for ($i = 1; $i <= 4; $i++) {
                            if (in_array($chamber->quadrant[$i], $elements)) {
                                $elementCount++;
                            }
                        }
                    }
                }
            }

            // Calculate the points for this cluster and update maxPoints if it's the highest
            $clusterPoints = $elementCount * $points;
            $maxPoints = max($maxPoints, $clusterPoints);
        }

        return $maxPoints;
    }
    public function calculateMaxPathPoints($element, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Determine if $element is a group type or a single element
        if (is_string($element) && isset($tokenTypes[$element])) {
            $elements = $tokenTypes[$element];  // It's a group type
        } elseif (is_string($element)) {
            $elements = [$element];  // It's a single element
        } else {
            throw new \Exception("Invalid element or group type provided.");
        }

        // Get the list of clusters from the dungeon
        $paths = $this->getPaths();

        // Initialize the maximum points
        $maxPoints = 0;

        // Iterate through each cluster
        foreach ($paths as $path) {
            $elementCount = 0;

            // Iterate through each chamber in the cluster
            foreach ($path as $row) {
                foreach ($row as $chamber) {
                    if ($chamber !== null) {
                        // Count the occurrences of the specified element(s) in the chamber's quadrants
                        for ($i = 1; $i <= 4; $i++) {
                            if (in_array($chamber->quadrant[$i], $elements)) {
                                $elementCount++;
                            }
                        }
                    }
                }
            }

            // Calculate the points for this cluster and update maxPoints if it's the highest
            $pathPoints = $elementCount * $points;
            $maxPoints = max($maxPoints, $pathPoints);
        }

        return $maxPoints;
    }

    public function calculatePointsForPathConnectedToExit($element, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Determine if $element is a group type or a single element
        if (is_string($element) && isset($tokenTypes[$element])) {
            $elements = $tokenTypes[$element];  // It's a group type
        } elseif (is_string($element)) {
            $elements = [$element];  // It's a single element
        } else {
            throw new \Exception("Invalid element or group type provided.");
        }

        // Get the list of paths from the dungeon
        $paths = $this->getPaths();

        // Initialize the points for the path connected to the exit
        $exitPoints = 0;

        // Iterate through each path
        foreach ($paths as $path) {
            // Check if the current path is connected to the exit
            if ($this->isConnectedToExit($path, $this->exit)) {
                $elementCount = 0;

                // Iterate through each chamber in the path
                foreach ($path as $row) {
                    foreach ($row as $chamber) {
                        if ($chamber !== null) {
                            // Count the occurrences of the specified element(s) in the chamber's quadrants
                            for ($i = 1; $i <= 4; $i++) {
                                if (in_array($chamber->quadrant[$i], $elements)) {
                                    $elementCount++;
                                }
                            }
                        }
                    }
                }

                // Calculate the points for this path connected to the exit
                $exitPoints = $elementCount * $points;
                break; // Since we're only interested in the path connected to the exit, we can stop here
            }
        }

        return $exitPoints;
    }

    public function calculateEdgePassagePoints($points)
    {
        // Initialize the count of edge passages and doors
        $edgeCount = 0;

        // Iterate through each row (1 to 4) and check the left and right edges
        for ($row = 1; $row <= 4; $row++) {
            // Check the left edge (column 1)
            if (isset($this->chambers[$row][1])) {
                $chamber = $this->chambers[$row][1];
                if ($chamber->direction['left']) { // if a passage covers a door, we don't double count
                    $edgeCount++;
                }
            }

            // Check the right edge (column 4)
            if (isset($this->chambers[$row][4])) {
                $chamber = $this->chambers[$row][4];
                if ($chamber->direction['right']) { // if a passage covers a door, we don't double count
                    $edgeCount++;
                }
            }
        }

        // Calculate the total points based on the number of edge passages or doors
        $totalPoints = $edgeCount * $points;

        return $totalPoints;
    }

    public function calculateTotalStarPoints($points)
    {
        // Access the token types from the game object
        $starElements = self::$game->token_types['stars'];

        // Initialize the count of stars
        $starCount = 0;

        // Iterate through each chamber in the dungeon
        foreach ($this->chambers as $row) {
            foreach ($row as $chamber) {
                if ($chamber !== null) {
                    // Count the occurrences of stars in the chamber's quadrants
                    for ($i = 1; $i <= 4; $i++) {
                        if (in_array($chamber->quadrant[$i], $starElements)) {
                            $starCount++;
                        }
                    }
                }
            }
        }

        // Calculate the total points based on the number of stars
        $totalPoints = $starCount * $points;

        return $totalPoints;
    }

    public function calculatePointsForFullChambers($points)
    {
        // Initialize the total points
        $totalPoints = 0;

        // Iterate through each chamber in the dungeon
        foreach ($this->chambers as $row) {
            foreach ($row as $chamber) {
                if ($chamber !== null) {
                    // Check if the chamber has four elements (no empty quadrants)
                    $elementCount = 0;
                    for ($i = 1; $i <= 4; $i++) {
                        if (!empty($chamber->quadrant[$i])) {
                            $elementCount++;
                        }
                    }

                    // If all four quadrants have elements, add points
                    if ($elementCount == 4) {
                        $totalPoints += $points;
                    }
                }
            }
        }

        return $totalPoints;
    }
    public function calculatePointsForChambersWithAny3Elements($elements, $points)
    {
        // Initialize the total points
        $totalPoints = 0;

        // Iterate through each chamber in the dungeon
        foreach ($this->chambers as $row) {
            foreach ($row as $chamber) {
                if ($chamber !== null) {
                    // Count the number of specified elements in the chamber's quadrants
                    $elementCount = 0;
                    for ($i = 1; $i <= 4; $i++) {
                        if (in_array($chamber->quadrant[$i], $elements)) {
                            $elementCount++;
                        }
                    }

                    // If at least three quadrants contain the specified elements, add points
                    if ($elementCount >= 3) {
                        $totalPoints += $points;
                    }
                }
            }
        }

        return $totalPoints;
    }

    public function calculatePointsForChambersWithAllElements($elements, $points)
    {
        // Initialize the total points
        $totalPoints = 0;

        // Iterate through each chamber in the dungeon
        foreach ($this->chambers as $row) {
            foreach ($row as $chamber) {
                if ($chamber !== null) {
                    // Check if all specified elements are present in the chamber's quadrants
                    $foundElements = array_fill_keys($elements, false);

                    for ($i = 1; $i <= 4; $i++) {
                        if (in_array($chamber->quadrant[$i], $elements)) {
                            $foundElements[$chamber->quadrant[$i]] = true;
                        }
                    }

                    // If all elements are found, add points
                    if (!in_array(false, $foundElements)) {
                        $totalPoints += $points;
                    }
                }
            }
        }

        return $totalPoints;
    }

    public function calculatePointsForUniqueElementsInGroup($elementGroup, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Check if the group exists in the token types
        if (!isset($tokenTypes[$elementGroup])) {
            throw new \Exception("Invalid element group provided.");
        }

        // Get the list of elements for the specified group
        $elements = $tokenTypes[$elementGroup];

        // Initialize a set to track unique elements
        $uniqueElements = [];

        // Iterate through each chamber in the dungeon
        foreach ($this->chambers as $row) {
            foreach ($row as $chamber) {
                if ($chamber !== null) {
                    // Check each quadrant for elements belonging to the specified group
                    for ($i = 1; $i <= 4; $i++) {
                        if (in_array($chamber->quadrant[$i], $elements)) {
                            $uniqueElements[$chamber->quadrant[$i]] = true;
                        }
                    }
                }
            }
        }

        // Calculate the total points based on the number of unique elements found
        $totalPoints = count($uniqueElements) * $points;

        return $totalPoints;
    }

    public function calculatePointsForCompleteSets($elements, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Initialize an array to count occurrences
        $elementCounts = [];

        // Initialize the counts for each element or group in the $elements array
        foreach ($elements as $element) {
            $elementCounts[$element] = 0;
        }

        // Iterate through each chamber in the dungeon
        foreach ($this->chambers as $row) {
            foreach ($row as $chamber) {
                if ($chamber !== null) {
                    // Check each quadrant for elements
                    for ($i = 1; $i <= 4; $i++) {
                        $quadrantElement = $chamber->quadrant[$i];

                        // If the element is a group, check if the quadrantElement belongs to that group
                        foreach ($elements as $element) {
                            if (isset($tokenTypes[$element])) {
                                // It's a group, check if the element is in this group
                                if (in_array($quadrantElement, $tokenTypes[$element])) {
                                    $elementCounts[$element]++;
                                }
                            } else {
                                // It's a single element, check directly
                                if ($quadrantElement === $element) {
                                    $elementCounts[$element]++;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Calculate the number of complete sets (using the minimum count)
        $completeSets = min($elementCounts);

        // Calculate the total points based on the number of complete sets
        $totalPoints = $completeSets * $points;

        return $totalPoints;
    }

    public function calculatePointsForUniformRowsOrColumns($points)
    {
        // Initialize the total points
        $totalPoints = 0;

        // Iterate through each row (1 to 4)
        for ($row = 1; $row <= 4; $row++) {
            if ($this->isUniform($this->chambers[$row])) {
                $totalPoints += $points;
            }
        }

        // Iterate through each column (1 to 4)
        for ($col = 1; $col <= 4; $col++) {
            $column = [];
            for ($row = 1; $row <= 4; $row++) {
                if (isset($this->chambers[$row][$col])) {
                    $column[] = $this->chambers[$row][$col];
                }
            }
            if ($this->isUniform($column)) {
                $totalPoints += $points;
            }
        }

        return $totalPoints;
    }

    private function isUniform($chambers)
    {
        $firstChamberType = null;

        foreach ($chambers as $chamber) {
            if ($chamber !== null) {
                if ($firstChamberType === null) {
                    $firstChamberType = $chamber->type;
                } elseif ($chamber->type !== $firstChamberType) {
                    return false;
                }
            }
        }

        return $firstChamberType !== null;
    }
    public function calculatePointsForEdgeElements($elementGroup, $points)
    {
        // Access the token types from the game object
        $tokenTypes = self::$game->token_types;

        // Check if the group exists in the token types
        if (!isset($tokenTypes[$elementGroup])) {
            throw new \Exception("Invalid element group provided.");
        }

        // Get the list of elements for the specified group
        $elements = $tokenTypes[$elementGroup];

        // Initialize the total points
        $totalPoints = 0;

        // Iterate through the leftmost column (column 1)
        for ($row = 1; $row <= 4; $row++) {
            if (isset($this->chambers[$row][1])) {
                $chamber = $this->chambers[$row][1];
                // Count occurrences of the elements in the chamber's quadrants
                for ($i = 1; $i <= 4; $i++) {
                    if (in_array($chamber->quadrant[$i], $elements)) {
                        $totalPoints += $points;
                    }
                }
            }
        }

        // Iterate through the rightmost column (column 4)
        for ($row = 1; $row <= 4; $row++) {
            if (isset($this->chambers[$row][4])) {
                $chamber = $this->chambers[$row][4];
                // Count occurrences of the elements in the chamber's quadrants
                for ($i = 1; $i <= 4; $i++) {
                    if (in_array($chamber->quadrant[$i], $elements)) {
                        $totalPoints += $points;
                    }
                }
            }
        }

        return $totalPoints;
    }

    public function calculatePointsForUniqueSizedClusters($points)
    {
        // Get the list of clusters from the dungeon
        $clusters = $this->getClusters();

        // Calculate the size of each cluster and store it in the clusterSizes array
        $clusterSizes = [];
        foreach ($clusters as $cluster) {
            $clusterSize = $this->calculateClusterSize($cluster);
            if ($clusterSize > 0) {
                $clusterSizes[] = $clusterSize;
            }
        }

        // Filter to keep only unique sizes
        $uniqueSizes = array_unique($clusterSizes);

        // Calculate the total points based on the number of unique sizes
        $totalPoints = count($uniqueSizes) * $points;

        return $totalPoints;
    }
}
