<?php


$content = file_get_contents(__DIR__.'/input/day14/input.txt');
$content = str_replace('mem','',$content);
$lines = explode("\n", trim($content," \n"));

$mask = '';
$memory = [];

function dec_to_bin($nr) {
	$n = $nr;
	$o = '';
	while ($n > 0) {
		$bit = $n % 2;
		$n = intdiv($n,2);
		
		$o = (($bit==1) ? '1':'0') .$o;
	}
	return str_pad($o,36,'0',STR_PAD_LEFT);
}
function bin_to_dec($nr) {
	$n = str_pad($nr,36,0,STR_PAD_LEFT);
	$o = 0;
	if (strlen($nr)<1) return $o;
	for ($i=0;$i<strlen($nr);$i++) {
		$o = $o *2  + intval(substr($nr,$i,1));
	}
	return $o;
}
//echo "<br/>".dec_to_bin(367051456);
//echo "<br/>".bin_to_dec('10101111000001100001011000000');


// apply mask the lazy way, instead of reading and setting bits
function applyMask($value,$mask) {
	$m = str_pad($mask,36,'X',STR_PAD_LEFT);
	$v = dec_to_bin($value);
	$v = str_pad($v,36,'0',STR_PAD_LEFT);
	
	$o = '';
	for ($i=0;$i<36;$i++) {
		$maskbit = substr($m,$i,1);
		$o .= ( $maskbit=='X') ? substr($v,$i,1) : $maskbit;
	}
	return bin_to_dec($o);
}



$total = 0;
foreach ($lines as $line) {
	$parts = explode ('=',$line);
	if (count($parts)==2) {
		$parts[0] = trim($parts[0],'[] ');
		$parts[1] = trim($parts[1]," \r"); // just in case a text editor adds the \r character
		if ($parts[0] =='mask') { 
			$mask = $parts[1];
		} else {
			$address = intval($parts[0]);
			$value = applyMask($parts[1],$mask);
			$memory[$address] = $value;
		}
	}
}

$total = 0;
foreach ($memory as $value) {
	$total += $value;
}
echo "part 1 answer = $total \n<br/>";


function generateAddresses($fromAddress, $mask) {
	
	if (strlen($mask)<36) die("mask < 36 $mask");
	$address = dec_to_bin($fromAddress);
	$a = '';
	$countx = 0;
	for ($i=0;$i<36;$i++) {
		$bit = substr($address,$i,1);
		$bit_mask = substr($mask,$i,1);
		if (($bit_mask=='X') || ($bit_mask=='1')) {
			$a .= $bit_mask;
			if ($bit_mask=='X') $countx++;
		} else {
			$a .= $bit;
		}
	}
	$nr = pow(2,$countx);
	//echo "prepared address = $a <br/>\n";
	//echo "x count= $countx nr= $nr <br/>\n";
	$list = [];
	for ($i=0;$i<$nr;$i++) {
		$bits = dec_to_bin($i);
		$bits = substr($bits,36-$countx,$countx);
		
		$a_new = '';
		for ($j=0;$j<36;$j++) {
			if (substr($a,$j,1)!='X') {
				$a_new .= substr($a,$j,1);
			} else {
				$a_new .= substr($bits,0,1);
				$bits = substr($bits,1);
			}
		}
		array_push($list,bin_to_dec($a_new));
	}
	
	return $list;
}
// test function above
//var_dump(generateAddresses(26,'00000000000000000000000000000000X0XX'));

// PART 2

$total = 0;
$mask = '';

$memory = [];

foreach ($lines as $line) {
	$parts = explode ('=',$line);
	if (count($parts)==2) {
		$parts[0] = trim($parts[0],'[] ');
		$parts[1] = trim($parts[1]," \r"); // just in case a text editor adds the \r character
		if ($parts[0] =='mask') { 
			$mask = $parts[1];
		} else {
			$addresslist = generateAddresses(intval($parts[0]),$mask);
			
			$value = intval($parts[1]);
			foreach ($addresslist as $idx => $address) { 
				$memory[$address] = $value;
			}
		}
	}
}

$total = 0;
foreach ($memory as $value) {
	$total += $value;
}
echo "part 2 answer = $total \n<br/>";





die();


?>