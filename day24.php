<?php
$content = trim(file_get_contents(__DIR__ .'/input/day24/input.txt'),"\n");
$lines = explode("\n",$content);

$blacktiles = 0;
foreach($lines as $line) {
    $s = $line; 
    $slen=strlen($line);
    $offset = 0;
    $state = false;
    while ($offset<$slen) {
        $chunk = substr($s,$offset,2);
        $jump = 1;
        if (strlen($chunk)==2) {
            // se, sw, w, nw, and ne
            if (($chunk=='ne') || ($chunk=='se') || ($chunk=='sw') || ($chunk=='nw')) $jump=2;
        }
        $state = ($state==false) ? true : false;
        $offset += $jump;
    }
    $blacktiles+= ($state==true) ? 1 : 0;
}

echo "\n part 1 answer=$blacktiles\n";
