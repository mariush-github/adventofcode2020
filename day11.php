<?php
 
$content = trim(file_get_contents(__DIR__.'/input/day11/input.txt')," \n");
$lines = explode("\n",$content);

$rows = count($lines);
$columns = strlen($lines[0]);

$grid = [];
// load the text file into a bi-dimensional array
for ($j=0;$j<$rows;$j++) {
    $grid[$j] = str_split($lines[$j]);
}
// keep a copy for part 2
$grid_org = $grid;

$grid_old = [];

$directions = [[-1,-1], [0,-1], [1,-1],[-1,0],[1,0],[-1,1],[0,1],[1,1]];

// mode = false : just the first cell around position, if true keep looking until chair found or outside area
function getCellType($x,$y,$direction,$mode=FALSE) {
    global $grid, $rows,$columns;
    $continue = true; 
    $offset_x = $direction[0];
    $offset_y = $direction[1];
    $cellType = '.';
    $pos_x = $x;
    $pos_y = $y;
    while ($continue) {
        $pos_x = $pos_x + $offset_x;
        $pos_y = $pos_y + $offset_y;
        if (($pos_x <0)||($pos_x>=$columns)) $continue=false;
        if (($pos_y<0) || ($pos_y>=$rows)) $continue=false;
        if ($continue) {
            $cellType = $grid[$pos_y][$pos_x];
            if (($cellType=='L')||($cellType=='#')) $continue=false;
        } 
        if ($mode==FALSE) $continue=FALSE;
    }
    return $cellType;
}

function snapshot($grid) {
    global $rows, $columns;
    $data = '';
    for ($j=0;$j<$rows;$j++) {
        $data .= implode('',$grid[$j])."\n";
    }
    return $data;
}

function work($scan_mode,$chairs_min = 4) {
    global $grid,$rows,$columns,$directions; 

    $grid_new = $grid; 
    $snapshot_prev = '';
    $snapshot_curr = snapshot($grid);

    while ($snapshot_curr != $snapshot_prev) {
        $snapshot_prev = $snapshot_curr;
        for ($j=0;$j<$rows;$j++) {
            for ($i=0;$i<$columns;$i++) {
                $grid_new[$j][$i] = $grid[$j][$i];
                $chairs_used = 0;
                for ($k=0;$k<8;$k++) {
                    $cellt = getCellType($i,$j,$directions[$k],$scan_mode);
                    if ($cellt=='#')  $chairs_used++;
                }
                if (($grid[$j][$i]=='L') && ($chairs_used==0)) $grid_new[$j][$i] = '#';
                if (($grid[$j][$i]=='#') && ($chairs_used>=$chairs_min)) $grid_new[$j][$i] = 'L';            
            }
        }
        $snapshot_curr = snapshot($grid_new);
        $grid = $grid_new;
        //echo $snapshot_curr."\n";
    }

    echo "Seats unused : ". strlen(str_replace(["\n","#","."],['','',''],$snapshot_curr))."\n";
    echo "Seats   used : ". strlen(str_replace(["\n","L","."],['','',''],$snapshot_curr))."\n";

}

// part 1
$result = work(false,4);
// part 2
$grid = $grid_org;
$result = work(true,5);

?>