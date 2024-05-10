<?php
function formatForSQLQuery($string, $nullable = true){
    if (is_null($string)) return ($nullable)?'NULL':0;
    if (is_numeric($string)) return $string;
    return "'".addslashes($string)."'";
}
?>