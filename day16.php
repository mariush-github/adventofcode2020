<?php

$content = file_get_contents(__DIR__.'/input/day16/input.txt');
$content = str_replace("\r",'',$content);

$pos1 = strpos($content,'your ticket:');
$pos2 = strpos($content,'nearby tickets:');
$content1 = trim(substr($content,0,$pos1)," \n");
$content2 = trim(substr($content,$pos1+12,$pos2-$pos1-12)," \n");
$content3 = trim(substr($content,$pos2+15)," \n");

$rules = [];
$myticket = [];
$tickets = [];
$rulesCount = 0;
$ticketsCount = 0;

function checkValueAgainstRules($value) {
	global $rules;
	foreach ($rules as $id => $rule) {
		foreach($rule['ranges'] as $range) {
			if (($value>=$range[0]) && ($value<=$range[1])) return TRUE;
		}
	}
	return FALSE;
}

function checkValueAgainstRulesWithArray($value) {
	global $rules;
	$result = [];
	foreach ($rules as $ruleID => $rule) {
		$result[$ruleID] = false;
		foreach($rule['ranges'] as $range) {
			if (($value>=$range[0]) && ($value<=$range[1])) $result[$ruleID]=true;
		}
	}
	return $result;
}




// parse the rules


$list = explode("\n",$content1);
$counter = 0;
foreach ($list as $l) {
	$parts = explode(':',$l);
	$ranges = explode('or',trim($parts[1]));
	$name = trim($parts[0]);
	$rules[$counter] = array('name'=>$name,'ranges'=>[]);
	foreach ($ranges as $range) {
		$r = explode('-',trim($range));
		array_push($rules[$counter]['ranges'],[intval($r[0]),intval($r[1])]);
	}
	$counter++;
}
$rulesCount = count($rules);
//var_dump($rules);
// process my ticket

$numbers = explode(',',$content2);

$myticket = [];
foreach ($numbers as $number) {
	if ((trim($number)!='') && (ctype_digit($number)==true)) array_push($myticket,intval($number));
}
//var_dump($myticket);

// process tickets 
$tickets = [];


$lines = explode("\n",$content3);
foreach ($lines as $line) {
	if (trim($line)!='') {
		$numbers  = explode(',',$line);
		$ticket = [];
		foreach ($numbers as $number) {
			if ((trim($number)!='') && (ctype_digit($number)==true)) array_push($ticket,intval($number));
		}
		if (count($ticket)>0) array_push($tickets,$ticket);
	}
}
//var_dump($tickets);
$ticketsCount = count($tickets);

echo "Loaded $rulesCount rules and $ticketsCount tickets.<br/>\n";

// Part 1  (and prep work for part 2)

$total = 0;
$valid = [];
$invalidCount = 0;
foreach ($tickets as $index => $ticket) {
	$valid[$index] = true; 
	foreach ($ticket as $number) {
		if (checkValueAgainstRules($number)==FALSE) { 
			$total+= $number;
			$valid[$index] = false;
		}
	}
	if ($valid[$index]==false) $invalidCount++;
}
echo "Part 1 answer = $total <br/>\n";
echo "There are $invalidCount invalid tickets. <br/>\n";


// Part 2 

$validations = [];

for ($column = 0;$column < $rulesCount; $column++) {
	// copy the column numbers from valid tickets into a temporary array
	$numberSet = [];
	for ($ticketNr = 0;$ticketNr<$ticketsCount;$ticketNr++) {
		if ($valid[$ticketNr]==true) array_push($numberSet,$tickets[$ticketNr][$column]);
	}
	array_push($numberSet,$myticket[$column]);
	//just for debugging reasons
	//echo "Testing column $column numbers : ".json_encode($numberSet)."<br/>\n";
	
	// assume this column has all numbers valid for all rules/
	// as we go through all the valid tickets, we'll find numbers that invalidate some rules 
	// ideally we end up with only 1 valid rule for all numbers in the valid tickets
	$validForRule = [];
	for ($i=0;$i<$rulesCount;$i++) $validForRule[$i]=true;

	
	foreach ($numberSet as $nr) {
		// returns what rules are valid for this particular number in the set
		$result = checkValueAgainstRulesWithArray($nr);
		// if the number is bad for one rule, then this column is obviously not for that rule 
		foreach ($result as $ruleID => $value) {
			if ($value==false) $validForRule[$ruleID] = false;
		}
	}
	//debugging 
	//echo "Column is valid for the following rules: ".json_encode($validForRule)." <br/>\n";
	$validations[$column] = $validForRule;
}

$positions = [];

$valids = [];
foreach ($validations as $columnID => $validation) {
	$valids[$columnID] = '';
	foreach ($validation as $ruleID => $value ) {
		$valids[$columnID] .= ($value==true) ? 1 : 0;
	}
}
function debug_showPattern() {
	global $valids;
	$text = "\n";
	foreach ($valids as $key => $value) {
		$text .= $value . "  <br/>\n";
	}
	echo $text;
	
}

//echo "original pattern : \n";
//debug_showPattern();


$found = true; 
while ($found==true) {
	$found = false;
	
	$columnWithOneTrue = -1;
	$columnWithOneTrueRuleID = -1;
	foreach ($valids as $columnID => $pattern) {
		$falseCount = strlen(str_replace('1','',$pattern));
		$trueCount = strlen(str_replace('0','',$pattern));
		if ($trueCount==1) {
			$columnWithOneTrue = $columnID;
			$columnWithOneTrueRuleID = strpos($pattern,'1');
			$found = true;
		}
	}
	if ($found == true) {
		echo "\n<br/>Found column $columnWithOneTrue matching rule $columnWithOneTrueRuleID \n <br/>";
		$positions[$columnWithOneTrue] = $columnWithOneTrueRuleID;
		foreach ($valids as $columnID => $pattern) {
			$newPattern = '';
			if ($columnWithOneTrueRuleID==0) $valids[$columnID] = '0'.substr($pattern,1);
			if ($columnWithOneTrueRuleID==strlen($pattern)-1) $valids[$columnID] = substr($pattern,0,strlen($pattern)-1).'0';
			if (($columnWithOneTrueRuleID > 0) && ($columnWithOneTrueRuleID< (strlen($pattern)-1))) {
				$valids[$columnID] = substr($pattern,0,$columnWithOneTrueRuleID).'0'.substr($pattern,$columnWithOneTrueRuleID+1);
			}
		}
		//echo "\n New pattern is: \n";
		//debug_showPattern();
	}
}

// now let's print the info nicely. 
$total = 1;
echo "My ticket info: \n";
for ($column = 0; $column<$rulesCount;$column++) {
	$name = $rules[$positions[$column]]['name'];
	echo '<br />Rule '.$positions[$column].' - '.$name.' : '.$myticket[$column]."\n";
	if (stripos($name,'departure')!==FALSE) $total = $total * $myticket[$column];
}
echo "Part 2 answer = $total \n";
	

?>