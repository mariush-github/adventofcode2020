<?php

$content = trim(file_get_contents(__DIR__.'/input/day13/input.txt')," \n");
$lines = explode("\n",$content);
$time_arrival = intval($lines[0]);
$buses = [];
$codes = explode(',',$lines[1]);

foreach ($codes as $index=>$value) {
    if (ctype_digit($value)==true) {
        array_push($buses,intval($value));    
    } else {
        array_push($buses,0);
    }
}

$multiples = [];
foreach ($buses as $index=>$value) {
    if ($value!=0) $multiples[$value] = intval(ceil($time_arrival/$value)*$value);
}

//var_dump(json_encode($multiples));
asort($multiples);
echo " time=".$time_arrival."\n";
echo "buses= ".json_encode($multiples)."\n";
$keys = array_keys($multiples);
$key = $keys[0];
echo "Part1= ".($key * ($multiples[$key]-$time_arrival))."\n";

// test with example 
//$lines[1] = '7,13,x,x,59,x,31,19';

// Part 2 inspired from https://github.com/constb/aoc2020/blob/main/13/index2.js
// not familiar with chinese remainder theorem
//
$buses = [];
$values = explode(',',$lines[1]);
for ($i=0;$i<count($values);$i++)  {
     if ($values[$i]!='x') {
        $modulo = intval($values[$i]);
        $v = intval($values[$i]); 
        $remainder = ($v - $i % $v) % $v;
        array_push($buses, array('modulo'=>$modulo, 'remainder'=>$remainder));
    }
}
//echo json_encode($buses)."\n";
$sorted = false;
while ($sorted==false) {
    $sorted = true;
    for ($i=1;$i<count($buses);$i++) {
        if ($buses[$i-1]['modulo']<$buses[$i]['modulo']) {
            $temp = $buses[$i-1];
            $buses[$i-1] = $buses[$i];
            $buses[$i] = $temp;
            $sorted = false;
        }
    }
}
//echo json_encode($buses);

$val = 0;
$step = 1;

for ($pos=0; $pos<count($buses); $pos++) {
  while ($val % $buses[$pos]['modulo'] !== $buses[$pos]['remainder']) $val += $step;
  $step *= $buses[$pos]['modulo'];
}

echo "Part 2 = $val\n";
die();


?>