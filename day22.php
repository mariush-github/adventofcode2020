<?php

$players = [];
$players[0] = [15,31,26,4,36,30,43,39,50,21,25,46,6,44,12,20,23,9,48,11,16,42,17,13,10];
$players[1] = [34,49,19,24,45,28,7,41,18,38,2,3,33,14,35,40,32,47,22,29,8,37,5,1,27];

$continue=true;
$turns = 0;
while ($continue=true) {
    $turns++;
    $a = array_shift($players[0]);
    $b = array_shift($players[1]);
    if (($a==0)||($b==0))  {
        $continue = FALSE;
        break;
    } else {
        if ($a>$b) {
            array_push($players[0],$a);
            array_push($players[0],$b);
        } else {
            array_push($players[1],$b);
            array_push($players[1],$a);
        }
        echo "\n Turn $turns :";
        echo "\n 1: ".$a;
        echo "\n 2: ".$b;
        echo "\n 1: ".json_encode($players[0]);
        echo "\n 2: ".json_encode($players[1]);
        echo "\n";
    }
    if (count($players[0])==0) break;
    if (count($players[1])==0) break;
}
$result = (count($players[0])>0) ? $players[0] : $players[1];

$j = 0;
$total = 0;
for ($i=count($result)-1;$i>=0;$i--) {
    $j++;
    $total += $j*$result[$i];

}
echo "\npart 1 = $total";

echo "\npart 2 starts in 10 seconds...";
sleep(10);

// part 2

$players = [];
$players[0] = [15,31,26,4,36,30,43,39,50,21,25,46,6,44,12,20,23,9,48,11,16,42,17,13,10];
$players[1] = [34,49,19,24,45,28,7,41,18,38,2,3,33,14,35,40,32,47,22,29,8,37,5,1,27];


// test sequence for infinite loop
//$players = [];
//$players[0] = [43, 19];
//$players[1] = [2,19,14];

// test sequence

//$players = [];
//$players[0] = [9, 2, 6, 3, 1];
//$players[1] = [5, 8, 4, 7, 10];

$glevel = 1;
$hashes = [];
function playGame( $data) {
    global $glevel;
    $myLevel = $glevel; $glevel++; 
    $players = [];
    $players[0] =$data[0];
    $players[1] =$data[1];
    echo "\n==== Game $myLevel ==== ";
    $continue=true;
    $turns = 0;
    $hashes = [];
    while ($continue==true) {
        $turns++;
        $hasha = implode(',',$players[0]);
        $hashb = implode(',',$players[1]);
        $hash = $hasha.'|'.$hashb;
        if (isset($hashes[$hash])==true) {
            return 1; // exit recursivity by return player 1 won
        }
        $hashes[$hash] = 1;
        echo "\n-- Round $turns (Game $myLevel) --";
        echo "\nPlayer 1 deck: ".$hasha;
        echo "\nPlayer 2 deck: ".$hashb;
        $a = array_shift($players[0]);
        $b = array_shift($players[1]);
        echo "\nPlayer 1 plays: ".$a;
        echo "\nPlayer 2 plays: ".$b;
        $result = 0;
        if (($a <= count($players[0])) && ($b<=count($players[1]))) {
            echo "\nPlaying a sub-game to determine the winner...";
            $playersNew = [];
            $playersNew[0]=[];
            $playersNew[1]=[];
            $counter=0; foreach ($players[0] as $idx=>$value) {$counter++;if ($counter<=$a) array_push($playersNew[0],$value);}
            $counter=0; foreach ($players[1] as $idx=>$value) {$counter++;if ($counter<=$b) array_push($playersNew[1],$value);}
            $result = playGame($playersNew);
            echo "\n...anyway, back to game $myLevel.";
        } else {
            if ($a>$b) $result=1;
            if ($b>$a) $result=2;
        }
        echo "\nPlayer $result wins round $turns of game $myLevel!";
        if ($result==1) {
            array_push($players[0],$a);
            array_push($players[0],$b);
        }
        if ($result==2) {
            array_push($players[1],$b);
            array_push($players[1],$a);
        } 
        if (count($players[0])==0) break;
        if (count($players[1])==0) break;
    }
    $winner = (count($players[0])==0) ? 2 : 1; 
    echo "\nThe winner of game $myLevel is player $winner!  [".count($players[0]).':'.count($players[1])."]";
    if ($myLevel==1) {
        echo "\n == Post-game results ==";
        echo "\nPlayer 1 : ".implode(',',$players[0]);
        echo "\nPlayer 2 : ".implode(',',$players[1]);
        $ret = Answer($players,$winner);
    }
    return $winner;
    
}
$ret = playGame($players);
function Answer($players,$winner) {
    $total = 0;
    $j=0;
    for ($i=count($players[$winner-1])-1;$i>=0;$i--) {
        $j++;
        $total += $j*$players[$winner-1][$i];
    }
    echo "\nPlayer score: $total";
}
?>