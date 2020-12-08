<?php

function codeRunner($inst) {
    $instCount = count($inst);
    $continue = true;
    $finishReason = '';
    $pos = 0;
    $acc = 0;
    $visited = [];
    for ($i=0;$i<$instCount;$i++) $visited[$i] = 0;
    while ($continue == true) {
        $instruction = $inst[$pos][0];
        if ($instruction == 'nop') { 
            $pos++;
        }
        if ($instruction == 'acc') {
            $acc += $inst[$pos][1];
            $pos++;
        }
        if ($instruction == 'jmp') {
            $pos = $pos + $inst[$pos][1];
            $visited[$pos]++;
            if ($visited[$pos]>1) $continue=false;
            $finishReason = 'loop';
        }
        if ($pos>=$instCount) {
            $continue=false;
            $finishReason = 'ok';
        }
    }
    return array('acc' => $acc, 'reason' => $finishReason, 'position' => $pos );
}

$content = file_get_contents(__DIR__ .'/input/day08/input.txt');
$lines = explode("\n",$content);

$inst = [];
$visited = [];
$acc = 0;
$pos = 0;
foreach ($lines as $line) {
    if (trim($line)!='') {
        $parts = explode(' ',trim($line));
        array_push($inst,[$parts[0],intval($parts[1])]);
    }
}
$instCount = count($inst);
for ($i=0;$i<$instCount;$i++) $visited[$i] = 0;
echo "Loaded $instCount instructions.\n";

$result = codeRunner($inst);



echo "Stopped at ".$result['position']." and accumulator value is ".$result['acc']." \n";
echo "Brute forcing ";
for ($i=0;$i<$instCount;$i++) {
    if (($inst[$i][0]=='nop') || ($inst[$i][0]=='jmp')) {
        $copy = $inst;
        $copy[$i][0] = ($copy[$i][0]=='nop') ? 'jmp' : 'nop';
        $result = codeRunner($copy);
        echo '.';
        if ($result['reason']=='ok') die ("\n Finished by changing instruction at offset $i, accumulator is ".$result['acc']."\n");
        
    }
}
?>