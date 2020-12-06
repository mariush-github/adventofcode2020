<?php

$content = file_get_contents(__DIR__ .'/input/day06/input.txt');
$lines = explode("\n",$content);

$group = [];

function countQuestions1($group) {
    $list = [];
    foreach ($group as $line) {
        for ($i=0;$i<strlen($line);$i++) $list[substr($line,$i,1)] = 1;
    }
    echo count($list)."\n";
    return count($list);
}

function countQuestions2($group) {
    $list = [];
    foreach ($group as $line) {
        
        for ($i=0;$i<strlen($line);$i++) {
            $letter = substr($line,$i,1);
            if (isset($list[$letter])==FALSE) $list[$letter] = 0;
            $list[$letter]++;
        }
    }
    $peopleCount = count($group);
    $total = 0;
    foreach ($list as $l => $value) {
        if ($value==$peopleCount) $total++;
    }
    echo $total."\n";
    return $total;
}

$total1 = 0;
$total2 = 0;

foreach ($lines as $line) {
    if ($line!='') {
        array_push($group,$line);
    } else {
        $total1 += countQuestions1($group);
        $total2 += countQuestions2($group);
        
        $group = [];
    }
}

echo "Part 1 = $total1 \n";
echo "Part 2 = $total2 \n";

?>