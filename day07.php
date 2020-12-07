<?php

$content = file_get_contents(__DIR__ .'/input/day07/input.txt');
$lines = explode("\n",$content);

$bags = [];

function addBag($bag) {
    global $bags;
    //if (($bag[0]=='') || ($bag[1]=='')) return FALSE;
    if (isset($bags[$bag[0]])==FALSE) $bags[$bag[0]] = [];
}
function decodeBag($text) {
    $t = explode(' ',trim($text));
    $qty = 0;
    $name = '';
    if (ctype_digit($t[0])==TRUE) {
        $qty = intval($t[0]);
        $t[0] = '';
    }
    $name = trim(implode(' ',$t));
    return [$name,$qty];
}

foreach ($lines as $index => $line) {
    if (trim($line)=='') break;
    $text = str_replace(['bags.', 'bags','bag.','bag'],'',$line);
    $text = str_replace('no other', '0 no other',$text);
    $lines[$index] = $text;
    //echo $text."\n";
}

foreach ($lines as $index => $line) {
    if (trim($line)!='') {
        //echo $line ."\n";
        $parts = explode('contain',$line);
        $leftBag = decodeBag($parts[0]);
        $rightBags = explode(',',$parts[1]);
        $ret = addBag($leftBag);
        //echo json_encode($leftBag).' : ';
        foreach ($rightBags as $text) {
            $rightBag = decodeBag($text);
            $ret = addBag($rightBag);
            //echo json_encode($rightBag);
            
            $bucketBag = &$bags[$leftBag[0]];
            array_push($bucketBag,$rightBag);
        }
    }
    //echo "\n";
}    

echo "There are a total of ".(count($bags)-1)." bags.";
function canHoldBagType($name,$bagtype='shiny gold') {
    global $bags;
    if ($name==$bagtype) return TRUE;
    if (isset($bags[$name])==FALSE) return FALSE;
    if (count($bags[$name]) == 0) return false;
    if (count($bags[$name])<1) return FALSE;
    for ($i=0;$i<count($bags[$name]);$i++) {
        $result = canHoldBagType($bags[$name][$i][0]);
        if ($result==TRUE) return TRUE;
    }
    return FALSE;
}

function countBags($name) {
    global $bags;
    $counter = 0;
    //echo "bag $name : \n";
    if (count($bags[$name])<1) return 0;
    
    for ($i=0;$i<count($bags[$name]);$i++) {
        $counter += $bags[$name][$i][1];
    }
    for ($i=0;$i<count($bags[$name]);$i++) {
        if ($bags[$name][$i][1] > 0) {
            //echo "adding bag ".$bags[$name][$i][0]." with ". $bags[$name][$i][1]." pcs\n";
            $counter = $counter + $bags[$name][$i][1] * countBags($bags[$name][$i][0]);
        }
    }
    //echo "returning $counter \n";
    return $counter;
}

$counter = 0;
foreach ($bags as $name => $bag) {
    if ($name != 'shiny gold') {
        if (canHoldBagType($name,'shiny gold')==TRUE) $counter++;
        //echo '.'.$name.'.';
    }
}

echo "Bags that can hold shiny gold bags: $counter \n";

$value = countBags('shiny gold');
echo "There are $value bags inside a shiny gold bag.";
?>