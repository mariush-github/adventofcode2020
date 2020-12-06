<?php

$sum = 2020;
$numbers  = [];

$content = file_get_contents(__DIR__ .'/input/day01/input.txt');
$content = str_replace("\r","\n",$content);

$n = explode("\n",$content);
foreach ($n as $value) {
    if (trim($value)!='') array_push($numbers,intval($value));
}
sort($numbers);
// part 01 - 744475
for ($i=0;$i<count($numbers)-1;$i++) {
    for ($j=$i+1;$j<count($numbers);$j++) {
        if (($numbers[$i]+$numbers[$j]) == $sum) {
            echo $numbers[$i] . ' + ' . $numbers[$j] . ' = ' . $sum .' => x = '.($numbers[$i] * $numbers[$j])."\n";
        }
    }
}
// part 02 - 70276940

for ($i=0;$i<count($numbers)-2;$i++) {
    for ($j=$i+1;$j<count($numbers)-1;$j++) {
        for ($k=$j+1;$k<count($numbers);$k++) {
            if (($numbers[$i]+$numbers[$j]+$numbers[$k]) == $sum) {
                echo $numbers[$i] . ' + ' . $numbers[$j] . ' + ' . $numbers[$k] . ' = ' . $sum .' => x = '.($numbers[$i] * $numbers[$j] * $numbers[$k])."\n";
            }
        }
    }
}

?>