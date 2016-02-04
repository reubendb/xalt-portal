<?php

# PAY ATTENTION TO A SILLY SYNTAX 
# $array = []; DOESN'T WORKS :@#$@$#$ instead try 
# $array = array();
# 
#  [] works on command line but screws up in actual call dont know why 
#  wasted 1 hour of my life on this!
#
#
function wrapper($content, $length) {
    $array = array();
    while(strlen($content) > $length){
        if (substr_count($content, '/') > 0) {
            $pos = (strpos($content, '/', $length) ? strpos($content, '/', $length) : 0);
            if ($pos == 0) { break;} else{
                array_push($array, substr($content, 0, $pos) , "<br />/");
                // change $content to remaining string
                $content = substr($content, $pos + 1);    
            }
        } else {
            break;
        }
    }
    array_push($array, $content);
    $objPath = implode("", $array);

    return($objPath);
}
?>

