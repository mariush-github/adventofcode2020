<?php

$mul_w = 1024*1024*1024;
$mul_z = 1024*1024;
$mul_y = 1024;


$input = <<<IN
.###.###
.#.#...#
..##.#..
..##..##
........
##.#.#.#
..###...
.####...
IN;

$input_test = <<<IN
.#.
..#
###
IN;

// comment out to use actual input
//$input = $input_test;

$input = str_replace("\r",'',$input);

// start at some offset so we don't deal with negative numbers 
$x = 512;
$y = 512; 
$z = 512;
$w = 512;

$minmax = [];
$minmax[0] = [512,512];
$minmax[1] = [512,512];
$minmax[2] = [512,512];
$minmax[3] = [512,512];


function setCell(&$world, $x,$y,$z,$w=512,$value=1) {
	global $mul_z,$mul_y,$mul_w;
	global $minmax;
	$world[$w*$mul_w+$z*$mul_z + $y*$mul_y + $x] = $value;
	$coords = [$x,$y,$z,$w];
	for ($i=0;$i<4;$i++) {
		if ($coords[$i]<$minmax[$i][0]) $minmax[$i][0] = $coords[$i];
		if ($coords[$i]>$minmax[$i][1]) $minmax[$i][1] = $coords[$i];
	}
}
function getCell(&$world, $x,$y,$z,$w=512) {
	global $mul_w, $mul_z,$mul_y;
	$xyzw = $w*$mul_w + $z*$mul_z + $y*$mul_y + $x;
	$value = (isset($world[$xyzw])==true) ? $world[$xyzw] : 0;
	return $value;
}

function getCellActiveNeighbors (&$world,$x,$y,$z,$w=512) {
	$value = getCell($world,$x,$y,$z,$w);
	$activeNeighbors = 0 - $value;
	for ($c=$z-1;$c<=$z+1;$c++) {
		for ($b=$y-1;$b<=$y+1;$b++) {
			for ($a=$x-1;$a<=$x+1;$a++) {
				$activeNeighbors+= getCell($world,$a,$b,$c);
			}
		}
	}
	return $activeNeighbors;
}

function getCellActiveNeighbors4D(&$world,$x,$y,$z,$w) {
	$value = getCell($world,$x,$y,$z,$w);
	$activeNeighbors = 0 - $value;
	for ($d=$w-1;$d<=$w+1;$d++) {
		for ($c=$z-1;$c<=$z+1;$c++) {
			for ($b=$y-1;$b<=$y+1;$b++) {
				for ($a=$x-1;$a<=$x+1;$a++) {
					$activeNeighbors+= getCell($world,$a,$b,$c,$d);
				}
			}
		}
	}
	return $activeNeighbors;
}


function getCoordinates($value) {
	global $mul_w, $mul_z, $mul_y;
	$v = $value;
	$w = intdiv($v,$mul_w);
	$v = $v-($w*$mul_w);
	$z = intdiv($v,$mul_z);
	$v = $v - ($z*$mul_z);
	$y = intdiv($v,$mul_y);
	$x = $v - ($y*$mul_y);
	return [$x,$y,$z,$w];
}

function displayWorld($world) {
	global $minmax,$mul_w,$mul_z,$mul_y;
	$text = '';
	for ($w=$minmax[3][0];$w<=$minmax[3][1];$w++) {
		for ($z=$minmax[2][0];$z<=$minmax[2][1];$z++) {
			$text .= "<br/> z=$z, w=$w \n<br/>";
			for ($y=$minmax[1][0];$y<=$minmax[1][1];$y++) {
				for ($x=$minmax[0][0];$x<=$minmax[0][1];$x++) {
					$text .= (getCell($world,$x,$y,$z,$w)==1) ? '#' : '.';
				}
				$text .= " <br/>\n";
			}
			$text .= " <br/><br/> \n\n";
		}
	}
	echo '<p><span style="font-family:Courier New">'."\n".$text."\n</span></p>";
}


function parseInput() {
	global $input,$world,$x,$y,$z,$w,$minmax;
	
	// start at some offset so we don't deal with negative numbers - resets (resets at part 2 start)
	$x = 512;
	$y = 512; 
	$z = 512;
	$w = 512;

	$minmax = [];
	$minmax[0] = [512,512];
	$minmax[1] = [512,512];
	$minmax[2] = [512,512];
	$minmax[3] = [512,512];

	$lines = explode("\n",$input);
	foreach ($lines as $line ) {
		$l = trim($line);
		if ($line!='') {
			for ($i=0;$i<strlen($l);$i++) {
				if (substr($l,$i,1)=='#') setCell($world,$x+$i,$y,$z,$w);
			}
			$y++;
		}
	}
}

// our "pocket universe" where we store everything as w*mul_w + z*mul_z + y*mul_y + x;
$world = []; 

// parse input 

$ret = parseInput();


echo "<br/> Start : \n";
displayWorld($world);
$newWorld = [];
for ($cycle = 1;$cycle<7;$cycle++) {
	$newWorld = [];
	for ($c=$minmax[2][0]-1;$c<=$minmax[2][1]+1;$c++) {
		for ($b=$minmax[1][0]-1;$b<=$minmax[1][1]+1;$b++) {
			for ($a=$minmax[0][0]-1;$a<=$minmax[0][1]+1;$a++) {
				$activeNeighbors = getCellActiveNeighbors4D($world,$a,$b,$c,512);
				$value = getCell($world,$a,$b,$c);
				if (($value==1) && ($activeNeighbors==2)) $ret = setCell($newWorld,$a,$b,$c);	// active and 2 or 3 neighbors - stay active
				if (($value==1) && ($activeNeighbors==3)) $ret = setCell($newWorld,$a,$b,$c);
				if (($value==0) && ($activeNeighbors==3)) $ret = setCell($newWorld,$a,$b,$c);	// inactive but 3 neighbors - become active
			}
		}
	}
	
	$world = $newWorld;
	echo "<br/> Iteration $cycle: \n";
	echo "<br/>";
	displayWorld($world);
	$counter = 0;
	foreach ($world as $xyz => $value) {
		$counter += $value;
	}
	echo "<br/> Active = $counter \n";
		
}

$counter = 0;
foreach ($world as $xyz => $value) {
	$counter += $value;
	
}
echo "<br/> Part 2 answer = $counter \n";

// -- part 2
$world = [];
$ret = parseInput();

echo "<br/> Start : \n";
displayWorld($world);
$newWorld = [];
for ($cycle = 1;$cycle<7;$cycle++) {
	$newWorld = [];
for ($d=$minmax[3][0]-1;$d<=$minmax[3][1]+1;$d++) {	
	for ($c=$minmax[2][0]-1;$c<=$minmax[2][1]+1;$c++) {
		for ($b=$minmax[1][0]-1;$b<=$minmax[1][1]+1;$b++) {
			for ($a=$minmax[0][0]-1;$a<=$minmax[0][1]+1;$a++) {
				$activeNeighbors = getCellActiveNeighbors4D($world,$a,$b,$c,$d);
				$value = getCell($world,$a,$b,$c,$d);
				if (($value==1) && ($activeNeighbors==2)) $ret = setCell($newWorld,$a,$b,$c,$d);	// active and 2 or 3 neighbors - stay active
				if (($value==1) && ($activeNeighbors==3)) $ret = setCell($newWorld,$a,$b,$c,$d);
				if (($value==0) && ($activeNeighbors==3)) $ret = setCell($newWorld,$a,$b,$c,$d);	// inactive but 3 neighbors - become active
			}
		}
	}
}
	
	$world = $newWorld;
	echo "<br/> Iteration $cycle: \n";
	echo "<br/>";
	displayWorld($world);
	$counter = 0;
	foreach ($world as $xyz => $value) {
		$counter += $value;
	}
	echo "<br/> Active = $counter \n";
		
}

$counter = 0;
foreach ($world as $xyz => $value) {
	$counter += $value;
	
}
echo "<br/> Part 1 answer = $counter \n";

?>