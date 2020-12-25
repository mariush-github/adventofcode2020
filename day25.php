<?php

$keys=[5764801,17807724];

$keys=[8252394,6269621];
$loops = [];
function findLoopSize($key) {
    $number = 7;
    $value = 1;
    $loop=1;
    while ($value!=$key) {
        $value = $value * $number;
        $value = $value % 20201227;
        //echo "\n loop=$loop value=$value ";
        $loop++;
    }
    return $loop-1;
}
function transformNumber($number,$loops){
    $n = $number;
    $value = 1;
    for ($i=0;$i<$loops;$i++) {
        $value = $value * $n;
        $value = $value % 20201227;
    }
    return $value;
}
for ($i=0;$i<2;$i++) {
    $key = $keys[$i];
    $loops[$i] = findLoopSize($key);
}
echo "\n card loops: ".$loops[0];
echo "\n door loops: ".$loops[1];
echo "\n private key = ".transformNumber($keys[1],$loops[0]);
echo "\n private key = ".transformNumber($keys[0],$loops[1]);


?>