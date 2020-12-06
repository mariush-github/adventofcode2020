<?php

$sum = 2020;
$numbers  = [];

$content = file_get_contents(__DIR__ .'/input/day02/input.txt');

$lines = explode("\n",$content);

function validPassword1($min,$max,$letter,$phrase) {
    $lets = [];
    $length = strlen($phrase);
    $lets[$letter] = 0;
    if (strlen($phrase)<1) return FALSE;
    for ($i=0;$i<$length;$i++) {
        $l = substr($phrase,$i,1);
        if (isset($lets[$l])==FALSE) $lets[$l] = 0;
        $lets[$l]++;
    }
    if (($lets[$letter]>=$min) && ($lets[$letter]<=$max)) return TRUE;
    return FALSE;
}

function validPassword2($first,$second,$letter,$phrase) {
    $a = substr($phrase,$first-1,1);
    $b = substr($phrase,$second-1,1);
    if ($a==$b) return FALSE;
    if (($a!=$letter) && ($b!=$letter)) return FALSE;
    return TRUE;
}

$counter1 = 0;
$counter2 = 0;

foreach ($lines as $line) {
    $parts = explode(' ',$line);
    if (count($parts)==3) {
        $numbers = explode('-',$parts[0]);
        $min = intval($numbers[0]);
        $max = intval($numbers[1]);
        $letter = trim($parts[1],": ");
        $pass = $parts[2];

        $valid = validPassword1($min,$max,$letter,$pass);
        if ($valid) $counter1++;
        $valid = validPassword2($min,$max,$letter,$pass);
        if ($valid) $counter2++;
        
    }
}
echo $counter1."\n";
echo $counter2."\n";
?>