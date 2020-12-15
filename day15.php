<?php

$mask = 0x20000000;

$input = [1,20,11,6,12,0];
//$input = [0,3,6]; 
$history = [];

for ($i=0;$i<count($input);$i++) {
    $history[$input[$i]] = $i+1;
}

$prev = $input[count($input)-1];
for ($i=count($input)+1;$i<30000001;$i++) {
    $a = intdiv($history[$prev],$mask);
    $b = $history[$prev] % $mask;
    $value = 0;
    //echo "i=$i prev=$prev a=$a b=$b ";
    if ($a!=0) { // just one occurance
        $value = $b-$a;
    }
    //echo "value=$value \n";
    if (isset($history[$value])==false) $history[$value] = 0;
    $a = intdiv($history[$value],$mask);
    $b = $history[$value] % $mask;
    $history[$value]= $b * $mask + $i;
    
    $prev = $value;

    if ($i % 100000 ==0) echo '.';
    if ($i==2020) echo "\n$i $value \n";
    if ($i==30000000 ) echo "\n$i $value \n";
}
?>