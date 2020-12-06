<?php

$content = file_get_contents(__DIR__ .'/input/day04/input.txt');
$lines = explode("\n",$content);

$data = '';

function withinRange($min,$max,$number) {
    if (($number<$min)  || ($number > $max)) return FALSE;
    return TRUE;
}

function validateData($data,$detailed=false) {
    $required = ['byr', 'iyr', 'eyr',   'hgt', 'hcl', 'ecl','pid'];
    $pairs = explode(' ',$data);
    $dict = [];
    foreach ($pairs as $pair) {
        $pos = strpos($pair,':');
        if ($pos!==FALSE) {
            $dict[substr($pair,0,$pos)] = substr($pair,$pos+1);
        }
    }
    echo json_encode($dict);
    foreach ($required as $index=>$r) {
        if (isset($dict[$r])==FALSE) { echo "-$r \n"; return FALSE; }
        if ($detailed==true) {
            $value = $dict[$r];
            
/*  byr (Birth Year) - four digits; at least 1920 and at most 2002.
    iyr (Issue Year) - four digits; at least 2010 and at most 2020.
    eyr (Expiration Year) - four digits; at least 2020 and at most 2030.
    hgt (Height) - a number followed by either cm or in:
        If cm, the number must be at least 150 and at most 193.
        If in, the number must be at least 59 and at most 76.
    hcl (Hair Color) - a # followed by exactly six characters 0-9 or a-f.
    ecl (Eye Color) - exactly one of: amb blu brn gry grn hzl oth.
    pid (Passport ID) - a nine-digit number, including leading zeroes.
    cid (Country ID) - ignored, missing or not.

     */
            if ($r=='byr') {
                if (ctype_digit($value)==FALSE) return FALSE;
                if (withinRange(1920,2002,$value)==FALSE) return FALSE; 
            }
            if ($r=='iyr') {
                if (ctype_digit($value)==FALSE) return FALSE;
                if (withinRange(2010,2020,$value)==FALSE) return FALSE; 
            }
            if ($r=='eyr') {
                if (ctype_digit($value)==FALSE) return FALSE;
                if (withinRange(2020,2030,$value)==FALSE) return FALSE; 
            }
            if ($r=='pid') {
                if (ctype_digit($value)==FALSE) return FALSE;
                if (strlen($value)!=9) return FALSE; 
            }
            if ($r=='hgt') {
                if (strlen($value)<4) return FALSE; // at least xxcm or xxin
                if (strlen($value)>5) return FALSE; // at most 1xxcm or 1xxin
                $nr = substr($value,0,strlen($value)-2);
                $unit = substr($value,strlen($value)-2,2);

                if (ctype_digit($nr)==FALSE) return FALSE;
                if (($unit=='in')||($unit=='cm')) {
                    if (withinRange(($unit=='in') ? 59 : 150, ($unit=='in') ? 76 : 193,$nr)==FALSE) return FALSE;
                } else {
                    return FALSE;
                } 
            }
            if ($r=='hcl') {
                if (strlen($value)!=7) return FALSE; // # 6 hex digits
                if (substr($value,0,1)!='#') return FALSE;
                if (ctype_xdigit(substr($value,1))==FALSE)  return FALSE; 
            }
            // amb blu brn gry grn hzl oth
            if ($r=='ecl') {
                $found = false;
                if (strlen($value)!=3) return FALSE; // # 6 hex digits
                if (strpos('|amb|blu|brn|gry|grn|hzl|oth|','|'.$value.'|')===FALSE) return FALSE;
            }             
        }
    }

    echo "\n";
    return TRUE;
}

$counter = 0;
foreach ($lines as $line) {
    if ($line!='') {
        $data.= ' '.$line;
    } else {
        $valid = validateData($data);
        if ($valid) $counter++;
        $data = "";
    }
}

echo "Part 1 = $counter \n";

$counter = 0;
foreach ($lines as $line) {
    if ($line!='') {
        $data.= ' '.$line;
    } else {
        $valid = validateData($data,true);
        if ($valid) $counter++;
        $data = "";
    }
}

echo "Part 2 = $counter \n";