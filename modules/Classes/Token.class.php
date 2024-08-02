<?php

namespace Classes;

use APP_DbObject;
use BgaVisibleSystemException;

class TokenManager extends \APP_DbObject
{

    public function __construct($token_data = null)
    {
    }

    public function initTokenTable($token_data)
    {

        // Flatten the token_data array to easily randomize the rows
        $flattened_data = [];

        foreach ($token_data as $shape => $shape_data) {
            foreach ($shape_data as $type => $type_data) {
                $face = $type_data[0];
                $count = $type_data[1];

                for ($i = 0; $i < $count; $i++) {
                    $flattened_data[] = [
                        'token_shape' => $shape,
                        'token_face' => $face,
                        'token_type' => $type,
                        'token_location' => 'reserve',
                        'token_location_type' => 'reserve',
                        'token_location_slot' => null
                    ];
                }
            }
        }

        // Shuffle the flattened data to randomize the rows
        shuffle($flattened_data);

        // Execute the SQL insert for each row
        foreach ($flattened_data as $token) {
            $sql = sprintf(
                "INSERT INTO token (token_shape, token_face, token_type, token_location, token_location_type, token_location_slot) VALUES ('%s', '%s', %d, '%s', '%s', %d)",
                $token['token_shape'],
                $token['token_face'],
                $token['token_type'],
                $token['token_location'],
                $token['token_location_type'],
                $token['token_location_slot']
            );
            $this->DbQuery($sql);
        }

            
    }
    
    public function pickTokens($nbr = 1, $shape = null){
        $sql = "SELECT token_id FROM token WHERE token_location = 'reserve'";
        if ($shape) $sql .= " AND token_shape = '$shape'";
        $sql .= " ORDER BY RAND() LIMIT $nbr";

        $tokens =  $this->getObjectListFromDB($sql,true);
        $tokens = ($nbr > 1) ? $tokens : $tokens[0];

        return $tokens;
    }
    public function pickTokenForLocation($shape = null,$location,$location_type,$location_slot){
        $token = $this->pickTokens(1,$shape);
        $this->moveTokensToLocation($token,$location,$location_type,$location_slot); 
    }

    public function moveTokensToLocation($token_ids, $location, $location_type, $location_slot = null){
        if (!is_array($token_ids)) $token_ids = [$token_ids];
        foreach ($token_ids as $token_id){
            $sql = sprintf("UPDATE token SET token_location = '%s', token_location_type = '%s', token_location_slot = %d WHERE token_id = %d",
            $location,
            $location_type,
            $location_slot,
            $token_id);

            $this->DbQuery($sql);
        }
    }

    public function moveTokenToMarketCard($token_id,$market_card_id, $market_card_slot){
        $this->moveTokensToLocation($token_id,$market_card_id,"market",$market_card_slot);
    }

    public function moveTokenToChamberCard($token_id,$chamber_card_id, $quadrant){
        $this->moveTokensToLocation($token_id,$chamber_card_id,"chamber",$quadrant);
    }

    public function moveAllTokensFromLocation($from,  $to, $to_type = null, $from_slot = null, $to_slot = null){
        $sql = "SELECT token_id FROM token WHERE token_location = '$from'";
        if ($from_slot) $sql .= " AND token_location_slot = '$from_slot'";
        $tokens = $this->getObjectListFromDB($sql);
        $this->moveTokensToLocation($tokens, $to, $to_type, $to_slot);
    }

    public function getTokensInLocation($location, $location_type, $location_slot_or_section = null, $associative = true){
        $sql = "SELECT token_id FROM token WHERE token_location = '$location' AND token_location_type = '$location_type'";

        switch (gettype($location_slot_or_section)){
            case "string":
                $section = ($location_slot_or_section == "top") ? 1 : (($location_slot_or_section =="bottom") ? 2 : "");
                $sql .= " AND `token_location_slot` LIKE '$section%'";
                break;
            case "number":
                $sql .= " AND `token_location_slot` = $location_slot_or_section";
                break;
            default:
        } 

        $tokens_retrieved = $this->getObjectListFromDB($sql);
        
        $tokens = array();

        foreach ($tokens_retrieved as $token){
            $tokens[$token['token_id']] = new Token($token['token_id']);
            
        }
        if (!$associative) $tokens = array_values($tokens);
        return $tokens;
        

    }


}

class Token extends \APP_DbObject
{

    public function __construct($token_id)
    {

        $sql = "SELECT * FROM token WHERE token_id = $token_id";
        $token = $this->getObjectFromDB($sql);

        if ($token) {
            foreach ($token as $attribute => $value) {
                $this->$attribute = $value;
            }
        }
    }
}
