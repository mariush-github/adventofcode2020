<?php
define('TILE_WHITE',0);
define('TILE_BLACK',1);
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
//$content = 'nwwswee';
$lines = explode("\n",$content);
$map = [];

function updateTile($x,$y,$z) {
    global $map;
    $offset = $z*65536 + $y*256 + $x;
    if (isset($map[$offset])==FALSE) $map[$offset]=[$x,$y,$z,TILE_WHITE];
    $map[$offset][3] = ($map[$offset][3]==TILE_WHITE) ? TILE_BLACK : TILE_WHITE;
}
// get tile from current play data
function getTile($x,$y,$z) {
    global $map;
    $offset = $z*65536 + $y*256 + $x;
    if (isset($map[$offset])==FALSE) return TILE_WHITE;
    return $map[$offset][3]; 
}
// set tile into next play data
function setTile($x,$y,$z,$value) {
    global $mapNew;
    $offset = $z*65536 + $y*256 + $x;
    if (isset($map[$offset])==FALSE) $map[$offset]=[$x,$y,$z,$value];
    $map[$offset][3] = $value;
}

foreach($lines as $line) {
    $s = trim($line,"\r"); 
    $slen=strlen($line);
    $offset = 0;
    $state = false;
    $x = 128;
    $y = 128;
    $z = 128; 
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
            if (strlen($char)<1) die();
            $x += $a;
            $y += $b;
            $z += $c;
            //echo "\n$char $x $y $z";
        }
        $offset++;
    }
    updateTile($x,$y,$z);
}
$blacktiles =0;
foreach ($map as $index => $value) {
    if ($value[3]==TILE_BLACK) $blacktiles++;
}
echo "\n part 1 answer=$blacktiles\n";

// part 2

$mapNew=[];
?>