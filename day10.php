<?php

// read the adapters from file 
$lines = explode("\n", trim(file_get_contents(__DIR__.'/input/day10/input.txt')," \n"));

$adapters_indexed = [];

$jolts_max = 0;
foreach ($lines as $idx => $line) {	
	$value = intval($line);
	if ($jolts_max < $value) $jolts_max = $value;
	array_push($adapters_indexed,$value);
}

sort($adapters_indexed);
$adaptersCount = count($adapters_indexed);

echo "There are a total of $adaptersCount adapters. The highest jolts value is $jolts_max.\n";

$dif_one = 0;
$dif_three = 1;
for ($i=0;$i<$adaptersCount;$i++) {
	$dif = $adapters_indexed[$i]-(($i==0)? 0 : $adapters_indexed[$i-1]);
	if ($dif==1) $dif_one++;
	if ($dif==3) $dif_three++;
}
echo "1-dif = $dif_one , 3-dif = $dif_three , multiplied = ".($dif_one*$dif_three)."\n";

die();



?>