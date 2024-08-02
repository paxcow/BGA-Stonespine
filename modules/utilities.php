<?php
function formatForSQLQuery($string, $nullable = true){
    if (is_null($string)) return ($nullable)?'NULL':0;
    if (is_numeric($string)) return $string;
    if (is_bool($string)) return $string?1:0;
    return "'".addslashes($string)."'";
}
?>