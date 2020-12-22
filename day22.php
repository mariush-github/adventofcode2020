<?php

$players = [];
$players[0] = explode(',','15,31,26,4,36,30,43,39,50,21,25,46,6,44,12,20,23,9,48,11,16,42,17,13,10');
$players[1] = explode(',','34,49,19,24,45,28,7,41,18,38,2,3,33,14,35,40,32,47,22,29,8,37,5,1,27');

$continue=true;
$turns = 0;
while ($continue=true) {
    $turns++;
    $a = intval(array_shift($players[0]));
    $b = intval(array_shift($players[1]));
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
        echo "\n 0: ".$a;
        echo "\n 1: ".$b;
        echo "\n 0: ".json_encode($players[0]);
        echo "\n 1: ".json_encode($players[1]);
        echo "\n";
    }
    if (count($players[0])==0) break;
    if (count($players[1])==0) break;
}
$result = (count($players[0])>0) ? $players[0] : $players[1];
var_dump($result);
$j = 0;
$total = 0;
for ($i=count($result)-1;$i>=0;$i--) {
    $j++;
    $total += $j*$result[$i];

}
echo "\npart 1 = $total";
?>
