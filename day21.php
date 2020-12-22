<?php

// remove CR and , - from (contains a,b) - , then remove empty line at the end
$lines = explode("\n",trim(str_replace(["\r",','],'',file_get_contents(__DIR__.'/input/day21/input.txt'))," \n)"));

$foods = [];
$ingredients = [];
$allergens = [];

$afPairs = [];
$foodsCnt = 0;
$ingredientCount = [];
$potentialNames = [];

foreach ($lines as $line) {
	if (trim($line)!='') {
		// all foods have at least one allergen, so we can split by (contains 
		$parts = explode('(contains',$line);
		$food = [];
		$allergen = [];
		$ings_raw = explode(' ',$parts[0]);
		foreach ($ings_raw as $ing_raw) {
			$ing = trim($ing_raw);
			if ($ing!='') {
				$ingredients[$ing] = $foodsCnt; // last food the ingredient was found on - more meaningful than "true" or whatever
				array_push($food,$ing);
				if (isset($ingredientCount[$ing])==FALSE) $ingredientCount[$ing] = 0;
				$ingredientCount[$ing]++;
			}
		}
		$foods[$foodsCnt] = $food;
		$allergs_raw = explode(' ',trim($parts[1],")"));
		foreach ($allergs_raw as $allerg_raw) {
			$allerg = trim($allerg_raw);
			if ($allerg!='') {
				$allergens[$allerg] = $foodsCnt;
				//array_push($allergen,$allerg);
				array_push($afPairs,[$allerg,$foodsCnt]);
			}
		}
		//$allergens[$foodsCnt]=$allerg;
		$foodsCnt++;
	}
}
// print them for debugging purposes only 
echo "\n allergens : ";
foreach($allergens as $allergen=>$lastSeen) {
	echo "\n $allergen seen in foods: ";
	foreach ($afPairs as $i => $afPair) {
		if ($afPair[0]==$allergen) echo $afPair[1]." ";
	}
}
foreach ($allergens as $allergen=>$lastSeen) {
	echo "\n Figuring out allergen $allergen";
	$fCount = 0;
	$f = [];
	foreach ($afPairs as $i => $afPair) {
		if ($afPair[0]==$allergen) { $fCount++; array_push($f,$afPair[1]);}
	}
	echo "\n Allergen found in foods : ".json_encode($f);
	$words = [];
	foreach ($f as $idx =>$inglist) {
		foreach ($foods[$inglist] as $idx2 => $ingItem) {
			if (isset($words[$ingItem])==FALSE) $words[$ingItem] = 0;
			$words[$ingItem]++;
		}
	}
	asort($words);
	echo "\n potential ingredient names for allergen :";
	$potentialNames[$allergen] = [];
	foreach ($words as $word => $counter) {
		if ($counter==$fCount) array_push($potentialNames[$allergen],$word);
	}
	echo "\n". json_encode($potentialNames[$allergen],JSON_PRETTY_PRINT);
	
}

function extractSingle() {
	global $potentialNames,$matches ;
	foreach ($potentialNames as $allergen=>$names) {
		if (count($names)==1) {
			$allergenName = $names[0];
			$matches[$allergen] = $allergenName;
			echo "\n Allergen $allergen matched to $allergenName";
			unset($potentialNames[$allergen]);
			foreach ($potentialNames as $a=>$n) {
				$potentialNames[$a] = removeFromArray($potentialNames[$a],$allergenName);
			}
		}
	}
	
}

function removeFromArray($a,$word) {
	$aNew = [];
	foreach ($a as $w) {
		if ($w!=$word) array_push($aNew,$w);
	}
	return $aNew;
}

$matches = [];

$continue = true;
while (count($potentialNames)>0) {
	$ret = extractSingle();
}

foreach ($matches as $allergen => $ingName) {
	$ingredientCount[$ingName] = 0;
}
$total = 0;
foreach ($ingredientCount as $ingName => $count) {
	$total += $count;
}
echo "\nPart 1 answer: $total ";
var_dump($matches);
ksort($matches);
var_dump($matches);
$canonic = '';
foreach ($matches as $allergen => $name) {
	$canonic .=','.$name;
}

echo "\npart 2 answer: ".trim($canonic,",");



?>