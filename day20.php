<?php

$content = file_get_contents(__DIR__.'/input/day20/input_test.txt');
$content = str_replace("\r",'',$content);

$tiles = [];

$chunks = explode("\n\n",$content);
echo "Found ".(count($chunks)-1).' chunks'."\n";
foreach ($chunks as $chunk) {
    if (trim($chunk)!=='') {
        $tile = new clsTile($chunk);
        $tile->findAllEdges();
        array_push($tiles,$tile);
    }
}
function e_add($edgeString) {
    global $edges,$edgesi;
    if (isset($edges[$edgeString])==FALSE) $edges[$edgeString] = 0;
    $edges[$edgeString]++;
    $edgesi++;
}
$edgesi = 0;
$edges = [];
for ($i=0;$i<count($tiles);$i++) {
    $e = $tiles[$i]->allEdges;
    foreach ($e as $edge) {
        if ($edge['d']==0) {
            for ($j=0;$j<4;$j++) { e_add($edge[$j]); }
        }
    }
}
asort($edges);
var_dump($edges);
// the tiles that have the edges that 
// show up least often are the corner ones
$cornerTiles = []; 

// foreach ($edges as $edge => $value) {
//     echo "\n$edge\t$value";
// }

echo "There are ".count($edges).' unique edges. Added '.$edgesi.' edges.';

foreach ($edges as $edge => $value) {
    if ($value==8) {
        for ($j=0;$j<count($tiles);$j++) {
            if ($tiles[$j]->hasEdge($edge)==TRUE) $cornerTiles[$j] = $tiles[$j]->id;
        }
    }
}
var_dump($cornerTiles);
$solutions = [];
//add first tile to the "solution"

//keep adding tiles to the solution



class clsTile {
    public $id;
    private $bits;
    private $original;
    public $allEdges;
    function __construct($chunk) {
        $lines = explode("\n",$chunk);
        $this->id = trim(str_replace([':','Tile'],['',''],$lines[0]));
        $this->bits=[];
        for ($j=0;$j<10;$j++) {
            for ($i=0;$i<10;$i++) { array_push($this->bits, substr($lines[$j+1],$i,1));}
        }
        $this->original = $this->bits;
    }
    function reset() {
        $this->bits = $this->original;
    }
    function rotateLeft() {
        $map = [];
        for ($i=0;$i<100;$i++) { $map[$i] = 0; }
        for ($j=0;$j<10;$j++) {
            //$map[$j] = [];
            for ($i=0;$i<10;$i++) {
                $map[(9-$i)*10+$j] = $this->bits[$j*10+$i];
            }
        }
        $this->bits = $map;
    }
    function output() {
        $text = implode('',$this->bits);
        echo "\n";
        for ($j=0;$j<10;$j++) {
            echo "\n ".substr($text,$j*10,10);
        }
    }
    function flipVertical(){
        $map = []; 
        for ($i=0;$i<100;$i++) { $map[$i] = 0; }
        for ($j=0;$j<10;$j++) {
            for ($i=0;$i<10;$i++) { $map[(9-$j)*10+$i] = $this->bits[$j*10+$i]; }
        }
        $this->bits = $map;
    }
    function flipHorizontal() {
        $map = [];
        for ($i=0;$i<100;$i++) { $map[$i] = 0; }
        for ($j=0;$j<10;$j++) {
            for ($i=0;$i<10;$i++) { $map[$j*10+$i] = $this->bits[$j*10+9-$i]; }
        }
        $this->bits = $map;
    }
    function edges() {
        $top   = '';for ($i=0;$i<10;$i++) { $top .= $this->bits[$i];}
        $right = '';for ($i=0;$i<10;$i++) { $right .= $this->bits[$i*10 +9 ];}
        $bottom= '';for ($i=10;$i>0;$i--) { $bottom .= $this->bits[89+$i];}
        $left  = '';for ($i=10;$i>0;$i--) { $left .= $this->bits[($i-1)*10];}
        return array (0 => $left /* left */, 1 => $top /* top */ , 2 => $right /* right */, 3 => $bottom /* bottom */ /*'top'=>$a , 'right' => $b, 'bottom' => $c, 'left' => $d */ );
    }
    function findAllEdges() {
        $this->allEdges=[];
        $flips = 0;
        for ($flips=0;$flips<4;$flips++) {
            $flipH = 0;
            $flipV = 0;
            if (($flips==2) || ($flips==3)) $flipH = 1;
            if (($flips==1) || ($flips==3)) $flipV = 1;
            $this->reset();
            if ($flips==1) {
                $flipV=1; $this->flipVertical();
            }
            if ($flips==2) {
                $flipH=1; $this->flipHorizontal();
            }
            if ($flips==3) {
                $flipV=1; $this->flipVertical();
                $flipH=1; $this->flipHorizontal();
            }
            for ($rotation=0;$rotation<4;$rotation++) {
                $result = $this->edges(); 
                $result['r' ] = $rotation;
                $result['fh'] = $flipH;
                $result['fv'] = $flipV;
                $result['o' ] = $rotation*4+$flipH*2+$flipV;
                $result['d' ] = 0;
                array_push($this->allEdges,$result);
                $this->rotateLeft();
            }
        }
        $this->reset();
        // remove duplicates from the edges
        $eCount = count($this->allEdges);
        for ($i=0;$i<$eCount-1;$i++) {
            if ($this->allEdges[$i]['d']==0) {
                $a = $this->allEdges[$i];
                for ($j=$i+1;$j<$eCount;$j++) {
                    $b = $this->allEdges[$j];                    
                    if (($a[0]==$b[0]) && ($a[1]==$b[1]) && ($a[2]==$b[2]) && ($a[3]==$b[3])) {
                        $this->allEdges[$j]['d']=1;
                    }
                }
            }
        }
    }
    function hasEdge($edge) {
        for ($i=0;$i<count($this->allEdges);$i++) {
            if ($this->allEdges[$i]['d']==0) {
                $e = $this->allEdges[$i];
                if (($e[0]==$edge) || ($e[1]==$edge) || ($e[2]==$edge) || ($e[3]==$edge)) return TRUE;
            }
        }
        return FALSE;
    }
}

?>