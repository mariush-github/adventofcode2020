<?php

$evals = [];
$debug = false; // set to TRUE to see debugging messages 

$lines = explode("\n",str_replace("\r",'',file_get_contents(__DIR__.'/input.txt')));
$total = 0;
foreach ($lines as $line) {
	$l = trim($line);
	if ($l!='') {
		$result = evaluateComplex($l,0);
		$total = $total + $result;
	}
}
echo "part 1 answer = $total \n";

$total = 0;
foreach ($lines as $line) {
	$l = trim($line);
	if ($l!='') {
		$result = evaluateComplex($l,1);
		$total = $total + $result;
	}
}
echo "part 2 answer = $total \n";



// mode 0 = part 1 , mode 1 = part 2
function evaluateComplex($text,$mode = 0) {
	global $evals,$debug;
	$sequence = '('.str_replace([" ","\r","\n"],'',$text).')';
	$expressions = [];
	$levels = [];
	$level = -1;
	for ($i=0;$i<strlen($sequence);$i++) {
		$digit = substr($sequence,$i,1);
		//echo $digit;
		if ($digit=='(') { $level++; array_push($levels,array('from'=>$i, 'to'=>strlen($sequence),'level'=>$level)); } // 'to' will be updated when ) is found
		if ($digit==')') { $level--; $result = array_pop($levels); $result['to'] = $i; array_push($expressions,$result); }
	}
	// sort by level 
	if (count($expressions)>1) {
		$sorted = false;
		while ($sorted==false) {
			$sorted=true;
			for ($i=0;$i<count($expressions)-1;$i++) {
				if ($expressions[$i]['level'] < $expressions[$i+1]['level']) { $temp = $expressions[$i+1]; $expressions[$i+1] = $expressions[$i]; $expressions[$i]=$temp; $sorted=false;}
			}
		}
	}
	if ($debug) {
		echo "\nsimple expressions list : ";
		foreach ($expressions as $i => $e) {
			echo "\n $i ".json_encode($e);
		}
		echo "\n";
	}
	//var_dump($expressions);
	// now evaluate all simple expressions and replace them in the big expression
	$evals = [];
	if (count($expressions)>0) {
		for ($i=0;$i<count($expressions);$i++) {
			$miniSeq = substr($sequence,$expressions[$i]['from']+1,$expressions[$i]['to']-$expressions[$i]['from']-1);
			if ($debug) echo "evaluating ".str_pad($miniSeq,50,' ',STR_PAD_LEFT)." : "; 
			$result = evaluateSimple($miniSeq,$mode) ;
			if ($debug) echo str_pad($result,6,' ',STR_PAD_LEFT)." ";
			$code = 'A'. dechex(count($evals));
			$evals[$code] = $result;
			$sequence = substr($sequence,0,$expressions[$i]['from']).str_pad($code,strlen($miniSeq)+2,' ',STR_PAD_RIGHT).substr($sequence,$expressions[$i]['to']+1);
			if ($debug) echo ". Stored as $code. New sequence is ".$sequence."\n";
			if ($i==count($expressions)-1) return $result;
		}
	}
}

// evaluates a simple chain of operations  without ( ) , the evaluateComplex detects 
// and replaces those with a hex code that starts with a letter A00 so the function below
// takes the value from the $evals array ... 
// 
// mode 0 : part 1 answer 
// mode 1 : part 2 answer 
//
function evaluateSimple($text,$mode=0) {
	global $evals,$debug;
	$seq = str_replace([" ","\r","\n",'(',')'],'',$text);
	//echo "\n evaluating $seq ";
	
	if ((substr($seq,0,1)=='+') || (substr($seq,0,1)=='*')) $seq = '0'.$seq;
	
	$tokens = [];
	$tockensCnt = 0;
	$i = 0;
	while ($i<strlen($seq)) {
		$digit = substr($seq,$i,1);
		if (ctype_xdigit($digit)==true) {
			$code = '';
			while ((ctype_xdigit($digit)==true) && ($i<strlen($seq))) {
				$code = $code.$digit; $i++;
				if ($i<strlen($seq)) $digit = substr($seq,$i,1);
			}
			array_push($tokens,$code);
		}
		if (($digit=='+') || ($digit=='*')) array_push($tokens,$digit);
		$i++;
	}
	//echo "\n tokens = ".json_encode($tokens); //var_dump($tokens);
	if ($mode==1) {
		// reduce all multiplications  - [ token 1 ]  [ + ] [ token 2]  = > [ * ] [ * ] [token 1 * token 2]
		// * tokens are basically silently ignored if multiple in a row. 
		if (count($tokens)>2) {
			if ($debug==true) echo "\n reducing ".json_encode($tokens);
			for ($i=1;$i<count($tokens)-1;$i++) {
				
				if ($tokens[$i]=='+') {
					$tokenIndex = $i;
					$a = $tokens[$tokenIndex-1]; if (ctype_digit(substr($a,0,1))==FALSE) $a = $evals[$a];
					$b = $tokens[$tokenIndex+1]; if (ctype_digit(substr($b,0,1))==FALSE) $b = $evals[$b];
					$tokens[$tokenIndex+1] = $a + $b;
					$tokens[$tokenIndex-1] = '1';
					$tokens[$tokenIndex] = '*';
					if ($debug) echo "\n ".json_encode($tokens);
				}
			}
			if ($debug) echo "\n";
		}
	}
	
	$total = 0;
	$operation = '+';
	foreach ($tokens as $token) {
		if (($token=='+') || ($token=='*')) {
			$operation = $token;
		} else {
			if (ctype_digit(substr($token,0,1))==FALSE) $token = $evals[$token];
			$total = ($operation=='+') ? ($total+$token) : ($total * $token);
		}
	}
	return $total;
	
}
?>