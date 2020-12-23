<?php

$cups = new CircularBuffer('364297581');

$moveCounter = 1;
function makeMove($million=true,$verbose=TRUE) {
	global $moveCounter,$cups;

	if ($verbose) echo "\n -- move $moveCounter --";
	if ($verbose) echo "\n cups: ".$cups->display();

	$cupValue = $cups->value();
	$extracted = [];
	$extractedKey = [];
	$cups->next();
	for ($i=0;$i<3;$i++) {
		$cup = $cups->extract();
		array_push($extracted,$cup);
		$extractedKey[$cup] = 1;
	}
	if ($verbose) echo "\n pick: ".implode(', ',$extracted);
	$cupToFind = $cupValue-1;
	if ($cupToFind<1) $cupToFind=($million==true) ? 1000000 : 9;;
	while (isset($extractedKey[$cupToFind])==TRUE) {
		$cupToFind--; if ($cupToFind<1) $cupToFind =($million==true) ? 1000000 : 9;
	}
	if ($verbose) echo "\n dest: $cupToFind";
	$cups->seek($cupToFind);
	foreach ($extracted as $v) { $cups->insert($v); }
	$cups->seek($cupValue);
	$cups->next();
	$moveCounter++;
}

for ($i=1;$i<101;$i++) {
	$result = makeMove(false,true);
}	
echo "\nafter 100 :".$cups->display();

// part 2 

// test example
//$cups = new CircularBuffer('389125467',true);

echo "\n";
$cups = new CircularBuffer('364297581',true);

$moveCounter = 1;
for ($i=1;$i<10000001;$i++) {
	$result = makeMove(true,false);
	if ($moveCounter % 100000 == 0) echo '.';
}
echo "\n";
$cups->seek(1);

$cups->next();
$a = $cups->value();
$cups->next();
$b = $cups->value();
echo "Part 2 answer = $a x $b = ".($a*$b);




class CircularBuffer {
	private $buffer;
	public $position; 
	private $nodeInsertID;
	private $quickFind;

	function __construct($code,$million=FALSE) {
		$this->buffer = [];
		$this->quickFind = [];
		for ($i=0;$i<(($million==true) ? 1000001 : 10); $i++) $this->quickFind[$i]=0; 
		for ($i=1;$i<10;$i++) {
			$v = intval(substr($code,$i-1,1));
			$piece = [$v,$i+1,$i-1];		// value, next,  previous
			$this->buffer[$i] = $piece;
			$this->quickFind[$v]=$i;
		}
		if ($million==true) {
			for ($i=10;$i<1000001;$i++) {
				$piece = [$i,$i+1,$i-1];
				$this->buffer[$i] = $piece;
				$this->quickFind[$i]=$i;
			}
		}
		$this->buffer[1][2] = ($million==true) ? 1000000 : 9;
		$this->buffer[($million==true)?1000000:9][1] = 1;
		$this->position = 1;
		$this->nodeInsertID = ($million==true)?1000001:10;
		echo "Loaded ".count($this->buffer)." nodes.";
		
	}
	function output() {
		echo "\n".json_encode($this->buffer);
	}
	function pos() {
		return $this->position;
	}
	function value() {
		return $this->buffer[$this->position][0];
	}
	function next() {
		$this->position = $this->buffer[$this->position][1];
	}
	function prev() {
		$this->position = $this->buffer[$this->position][2];
	}
	function extract() {
		$value = $this->buffer[$this->position][0];
		$next  = $this->buffer[$this->position][1];
		$prev  = $this->buffer[$this->position][2];
		unset($this->buffer[$this->position]);
		$this->buffer[$prev][1] = $next;
		$this->buffer[$next][2] = $prev;
		$this->position = $next;
		$this->quickFind[$value]=0;
		return $value;
	}
	function insert($value) {
		$id = $this->nodeInsertID;
		$this->nodeInsertID++;
		$nextNodeID = $this->buffer[$this->position][1];
		$this->buffer[$id]=[$value,$nextNodeID,$this->position];
		$this->buffer[$this->position][1] = $id;
		$this->buffer[$nextNodeID][2] = $id;
		$this->position = $id;
		$this->quickFind[$value]=$id;
	}
	
	function display() {
		$text = '';
		$posOriginal = $this->position;
		$pos = $this->buffer[$this->position][1];
		while ($pos!=$posOriginal) {
			$text .= ' '.(($this->position==$posOriginal)? '(' : '').$this->buffer[$this->position][0].(($this->position==$posOriginal)? ')' : '');
			$this->next();
			$pos = $this->position;
		}
		return trim($text);
	}
	function seek($value) {
		//while ($this->value()!=$value) $this->next();
		$this->position = $this->quickFind[$value];
	}
}


?>