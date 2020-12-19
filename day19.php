<?php


$content = <<<IN
0: 4 1 5
1: 2 3 | 3 2
2: 4 4 | 5 5
3: 4 5 | 5 4
4: "a"
5: "b"

ababbb
bababa
abbbab
aaabbb
aaaabbb
IN;
// comment line below to use test case
$content = file_get_contents(__DIR__.'/input/day19/input.txt');
$content = str_replace("\r",'',$content);
$content = str_replace("\"",'',$content);

$parts = explode("\n\n",$content);
$messages = explode("\n",$parts[1]);
$rules = [];
$lines = explode("\n",$parts[0]);
foreach ($lines as $line) {
    $l = trim ($line);
    if ($l!='') {
        $ruleID = intval(substr($l,0,strpos($l,':')+1));
        $ruleText = substr($l,strpos($l,':')+2);
        $parts = explode('|',$ruleText);
        foreach ($parts as $idx => $part) { $parts[$idx] = explode(' ',trim($part)); foreach ($parts[$idx] as $i=>$p) { $parts[$idx][$i]=(ctype_digit($p)==true) ? intval($p) : $p;} }
        $rules[$ruleID] = $parts;
    }
}



$replacers = [];

//echo json_encode($rules,JSON_PRETTY_PRINT);
foreach ($rules as $id => $rule) {
    if ((count($rule)==1) && (count($rule[0])==1)) $replacers[$id] = $rule[0][0];
}
// part 2 - fix 
unset($replacers[8]);
// var_dump($replacers);

// simplify rules a bit, reduce the recursion 

if (count($replacers)>0) {
    foreach ($rules as $ruleID => $rule) {
        foreach ($rule as $subRuleID => $subRule) {
            foreach ($subRule as $subRuleEntryID => $subRuleEntry) {
                if (isset($replacers[$subRuleEntry])==TRUE) $rules[$ruleID][$subRuleID][$subRuleEntryID] = $replacers[$subRuleEntry];
            }
        }
    }
}

// change rules to always have two bits in series  or 2 in parallel with 2, makes the other functions simpler

// foreach ($rules as $ruleID => $rule) { 
//     if (isset($rules[$ruleID][0][1])==FALSE) $rules[$ruleID][0][1] = '';
//     if (count($rule)>1) {
//         if (isset($rules[$ruleID][1][1])==FALSE) $rules[$ruleID][1][1] = '';
//     }
// }

function testSegment($message,$offset,$ruleID ) {
    global $rules;
    if ($ruleID==='') return '';
    if ($ruleID==='a') return (substr($message,$offset,1)==='a') ? 'a' : FALSE;
    if ($ruleID==='b') return (substr($message,$offset,1)==='b') ? 'b' : FALSE;

    $answers = [];

    $r = $rules[$ruleID];
    for ($i=0;$i<count($r);$i++){
        $answers[$i] = '';
        for ($j=0;$j<count($r[$i]);$j++) {
            if ($answers[$i]!==FALSE) { 
                $a = testSegment($message,$offset+strlen($answers[$i]),$r[$i][$j] );
                $answers[$i] = ($a===FALSE) ? FALSE : $answers[$i].$a;
            }
        }
        if ($answers[$i]!==FALSE) return $answers[$i];
    }
    return FALSE;
}

// debug : show the rules
foreach ($rules as $i=>$rule) { echo "\n $i ".json_encode($rule); }

$counter = 0;
foreach ($messages as $message) {
    if (trim($message)!='') {
        $result = testSegment($message,0,0);
        if ($result===$message) $counter++;
    }
}
echo "\npart 1 answer= $counter";


// part 2 is not correct, will try to fix it 
$rules[8] = [ [42],[42,8] ];
$rules[11] = [[42,31], [42,11,31]];
$counter = 0;
foreach ($messages as $message) {
    if (trim($message)!='') {
        $result = testSegment($message,0,0);
        if ($result===$message) $counter++;
    }
}
echo "\npart 2 answer= $counter";

?>