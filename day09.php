<?php

$lines = explode("\n", trim(file_get_contents(__DIR__.'/input/day09/input.txt')," \n"));
foreach ($lines as $idx => $line) {
	$lines[$idx] = intval($line);
}
function validateNumber($offset) {
	global $lines;
	for ($i=$offset-25;$i<$offset-1;$i++) {
		for ($j=$i+1;$j<$offset;$j++) {
			$sum = $lines[$i] + $lines[$j];
			if ($sum==$lines[$offset]) return TRUE;
		}
	}
	return FALSE;
}

$offset = 0;
$number = 0;

for ($i=25;$i<count($lines);$i++) {
	$result = validateNumber($i);
	if ($result==FALSE) {
		$offset = $i;
		$number = $lines[$i];
		echo "Error found at offset $i and the number is ".$lines[$i]."\n";
		break;
	}
}

$sums = [];
$s = 0;
$sums[0] = 0;
for ($i=0;$i<$offset;$i++) {
	$s += $lines[$i];
	$sums[$i+1] = $s;
}
$dec = 0;
$start = 0;
$continue = true;
$range_start = 0;
$range_finish = 0;
while ($continue) {
	for ($i=$start;$i<$offset;$i++) {
		if (($sums[$i]-$dec) == $number) {
			echo "Found range, starts at offset ".($start-1)." and ends at offset ".($i-1).": \n";
			$range_start = $start-1;
			$range_finish = $i-1;
			$continue=false;
			break;
		}
	}
	$dec = $sums[$start];
	$start++;
	if ($start>=$offset) $continue=false;
}
$extracted = [];
for ($i=$range_start;$i<=$range_finish;$i++) {
	array_push($extracted,$lines[$i]);
	echo str_pad($i,3,0,STR_PAD_LEFT).' : '.$lines[$i]."\n";
}
sort($extracted);

$result = $extracted[0] + $extracted[count($extracted)-1];

echo "Part 2 answer is $result.\n";





?>