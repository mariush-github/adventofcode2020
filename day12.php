<?php

$content = trim(file_get_contents(__DIR__.'/input/day12/input.txt')," \n");
$lines = explode("\n",$content);

/*
// test with small example 
$lines = explode("\r\n","F10
N3
F7
R90
F11");
*/

$commands = [];
foreach ($lines as $line) {
    array_push($commands,[substr($line,0,1),intval(substr($line,1))]);
}

function turn($LR, $direction) {
    $changeR = array('E'=>'S','S'=>'W','W'=>'N','N'=>'E');
    $changeL = array('E'=>'N','S'=>'E','W'=>'S','N'=>'W');
    if ($LR=='L') return $changeL[$direction];
    if ($LR=='R') return $changeR[$direction];
}

$direction = 'E';
$x = 0;
$y = 0;

// part 1 
foreach ($commands as $command) {
    
    $c = $command[0];
    $v = $command[1];
    //echo $c.$v.': ';
    if (($c=='L')||($c=='R')) {
        while ($v>0) {
            $direction = turn($c,$direction); $v = $v-90;
        }
    }
    if ($c=='F') {
        if ($direction=='E') $x += $v;
        if ($direction=='W') $x -= $v;
        if ($direction=='N') $y -= $v;
        if ($direction=='S') $y += $v;
    }
    if ($c=='E') $x += $v;
    if ($c=='W') $x -= $v;
    if ($c=='N') $y -= $v;
    if ($c=='S') $y += $v;

    //echo "O= $direction X= $x Y= $y \n";
}
echo "O= $direction X= $x Y= $y \n";
echo "result = ".(abs($x) + abs($y))."\n";

// part 2
$x = 0; // ship
$y = 0;
$w_x = 10; // waypoint
$w_y = -1;

function rotate($direction) {
    global $w_x,$w_y;
    $a_x = 0;
    $a_y = 0;
    if ($direction=='L') {$a_x = $w_y; $a_y=-$w_x;}
    if ($direction=='R') {$a_x = -$w_y; $a_y = $w_x;}
    $w_x = $a_x;
    $w_y = $a_y;
}

foreach ($commands as $command) {
    
    $c = $command[0];
    $v = $command[1];
    //echo $c.$v.': ';
    if (($c=='L')||($c=='R')) {
        while ($v>0) {
            $result =  rotate($c); $v = $v-90;
        }
    }
    if ($c=='F') {
        $x += $w_x * $v;
        $y += $w_y * $v;
        //if ($direction=='E') $x += $v;
        //if ($direction=='W') $x -= $v;
        //if ($direction=='N') $y -= $v;
        //if ($direction=='S') $y += $v;
    }
    if ($c=='E') $w_x += $v;
    if ($c=='W') $w_x -= $v;
    if ($c=='N') $w_y -= $v;
    if ($c=='S') $w_y += $v;

    //echo "X= $x Y= $y W_X = $w_x W_Y = $w_y\n";
}

echo "X= $x Y= $y W_X = $w_x W_Y = $w_y\n";
echo "result = ".(abs($x) + abs($y))."\n";
?>