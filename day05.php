<?php

$content = file_get_contents(__DIR__ .'/input/day05/input.txt');
$lines = explode("\n",$content);

function decodePosition($code){
    $chunk = 64;
    $min_y = 0;
    $max_y = 127;
    $min_x = 0;
    $max_x = 7;
    for ($i=0;$i<7;$i++) {
        $command = substr($code,$i,1);
        if ($command=='F') { $max_y = $min_y + $chunk -1;  }
        if ($command=='B') { $min_y = $max_y - $chunk +1;  }
        $chunk = intdiv($chunk,2); 
    }
    $chunk = 4;
    for ($i=7;$i<10;$i++) {
        $command = substr($code,$i,1);
        if ($command=='L') { $max_x = $min_x + $chunk -1;  }
        if ($command=='R') { $min_x = $max_x - $chunk +1;  }
        $chunk = intdiv($chunk,2); 
    }   

    return [$min_x,$min_y,$min_y*8+$min_x];

}
$seatMin = 1023;
$seatMax = 0;
$map = [];
for ($i=0;$i<1024;$i++) $map[$i] = 0;
foreach ($lines as $line) {
    if (strlen($line)==10) {
        $pos = decodePosition($line);
        echo $line.' '.json_encode($pos)."\n";
        if ($seatMin>$pos[2]) $seatMin = $pos[2];
        if ($seatMax<$pos[2]) $seatMax = $pos[2];
        $map[$pos[2]] = 1;
    }
}
echo "min seat = $seatMin \n";
echo "max seat = $seatMax \n";
for ($i=$seatMin+1;$i<$seatMax;$i++) {
    if (($map[$i]==0) && ($map[$i-1]==1) && ($map[$i+1]==1)) echo " my seat = $i \n";
}
?>