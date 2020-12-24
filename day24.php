<?php
define('TILE_WHITE',1);
define('TILE_BLACK',2);
// from https://www.redblobgames.com/grids/hexagons/
// Neighbors > Cube Coordinates
$directions = array(
    'nw' => [+0,-1,+1],
    'ne' => [+1,-1,+0],
    'e'  => [+1,+0,-1],
    'se' => [+0,+1,-1],
    'sw' => [-1,+1,+0],
    'w'  => [-1,+0,+1]
);

$content = trim(file_get_contents(__DIR__ .'/input/day24/input.txt'),"\n");

$lines = explode("\n",$content);
$map = [];

function flipTile($x,$y,$z) {
    global $map;
    $offset = $z*262144 + $y*512 + $x;
    if (isset($map[$offset])==FALSE) $map[$offset]=[$x,$y,$z,TILE_WHITE];
    $map[$offset][3] = ($map[$offset][3]==TILE_WHITE) ? TILE_BLACK : TILE_WHITE;
}
// get tile from current play data
function getTile($x,$y,$z) {
    global $map;
    $offset = $z*262144 + $y*512 + $x;
    if (isset($map[$offset])==FALSE) return TILE_WHITE;
    return $map[$offset][3]; 
}
// set tile into next play data
function setTile($x,$y,$z,$value) {
    global $mapNew;
    $offset = $z*262144 + $y*512 + $x;
    if (isset($mapNew[$offset])==FALSE) $mapNew[$offset]=[$x,$y,$z,$value];
    $mapNew[$offset][3] = $value;
}

foreach($lines as $line) {
    $s = trim($line,"\r"); 
    $slen=strlen($line);
    $offset = 0;
    $state = false;
    $x = 256;
    $y = 256;
    $z = 256; 
    while ($offset<$slen) {
        $char = substr($s,$offset,1);
        if (($char=='n') || ($char=='s')) {
            $offset++;
            $char .= substr($s,$offset,1);
        }
        if ($char!='') {
            $a = 0;
            $b = 0;
            $c = 0;
            list($a,$b,$c) = $directions[$char];
            //if (strlen($char)<1) die();
            $x += $a;
            $y += $b;
            $z += $c;
            //echo "\n$char $x $y $z";
        }
        $offset++;
    }
    flipTile($x,$y,$z);
}
$blackTiles =0;
foreach ($map as $index => $value) {
    if ($value[3]==TILE_BLACK) $blackTiles++;
}
echo "\n part 1 answer=$blackTiles\n";

// part 2



for ($i=1;$i<101;$i++ ) {
    $mapNew=[];  
    $mins = [];
    $maxs = [];
    for ($j=0;$j<3;$j++) { 
        $mins[$j]=1024;$maxs[$j]=0;
    }
  
    foreach ($map as $offset => $node) {
        for ($j=0;$j<3;$j++) { 
            if($mins[$j]>$node[$j]) $mins[$j] = $node[$j];
            if($maxs[$j]<$node[$j]) $maxs[$j] = $node[$j];
        }
    }
    //echo "\n MinMax= ".json_encode($mins).' : '.json_encode($maxs);
 

    for ($z=$mins[2]-1;$z<=$maxs[2]+1;$z++) {
        for ($y=$mins[1]-1;$y<=$maxs[1]+1;$y++) {
            for ($x=$mins[0]-1;$x<=$maxs[0]+1;$x++) {
                $offset = $z*262144 + $y*512 + $x;
                $value=getTile($x,$y,$z);
                //echo "\n $x $y $z $value";
                
                $blackTiles = 0;
                foreach ($directions as $idx => $dir) {
                    //echo "\n $idx  ".implode(',',$dir);
                    $tileSide = getTile($x+$dir[0],$y+$dir[1],$z+$dir[2]);
                    if ($tileSide==TILE_BLACK) $blackTiles++; 
                }
                //echo "\n x=$x, y=$y, z=$z, tile=$value, n=$blackTiles";
                /*
                Any black tile with zero or more than 2 black tiles immediately adjacent to it is flipped to white.
                Any white tile with exactly 2 black tiles immediately adjacent to it is flipped to black.
                */
                if (isset($map[$offset])==true) {
                    if ($map[$offset][3]==TILE_BLACK) $mapNew[$offset]=$map[$offset];
                }
                if ($value==TILE_BLACK) {
                    if ($blackTiles==0) setTile($x,$y,$z,TILE_WHITE); //setTile($x,$y,$z,TILE_WHITE); 
                    if ($blackTiles >2) setTile($x,$y,$z,TILE_WHITE); //setTile($x,$y,$z,TILE_WHITE);
                } 
                if ($value==TILE_WHITE) {
                    if ($blackTiles==2) setTile($x,$y,$z,TILE_BLACK); //setTile($x,$y,$z,TILE_BLACK);
                } 
            }
        }
    }
    $map = $mapNew;
    $blackTiles =0;
    foreach ($map as $offset => $node) {
        if ($node[3]==TILE_BLACK) $blackTiles++;
    }
    echo "\n part 2 , day $i answer=$blackTiles";
}
?>