<?php


$content = trim(file_get_contents(__DIR__ .'/input/day03/input.txt')," \n");
$lines = explode("\n",$content);
$original = $lines;

function rotateLines($distance) {
    global $lines;
    foreach ($lines as $idx =>$line) {
        $lines[$idx] = substr($line,$distance).substr($line,0,$distance);
    }
}

function countTrees($right,$down,$verbose=FALSE) {
    global $original;
    global $lines;
    $lines = $original;
    $lineCount = count($lines);

    if ($verbose) echo "lines = $lineCount\n";
    $x=$right;
    $y=$down;
    $trees = 0;
    while ($y<$lineCount) {
        $c = substr($lines[$y],$x,1);
        if ($verbose) echo $lines[$y].' '.$x.':'.$y.'='.$c."\n";
        rotateLines($right);
        $x=$right;
        $y=$y+$down;
        if ($c=='#') $trees++;
    }
    if ($verbose) echo "trees = $trees\n";
    return $trees;
}

echo "Part 1: ".countTrees(3,1,true)."\n";

$a = countTrees(1,1,false);
$b = countTrees(3,1,false);
$c = countTrees(5,1,false);
$d = countTrees(7,1,false);
$e = countTrees(1,2,false);
echo "Part 2: \n";
var_dump($a,$b,$c,$d,$e,$a*$b*$c*$d*$e);

?>