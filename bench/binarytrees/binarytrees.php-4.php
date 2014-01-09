<?php 
/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/
   binarytree by Ludovic Urbain
*/

function tree(&$n,&$i,&$depth){
   $n[2]=$i;
   if(!$depth){
      return $n;
   }
   $i2=$i+$i;
   $depth--;
   //tree($n[1],$i2,$depth); 
      $n[1][2]=$i2;
      if($depth){
         $i3=$i2+$i2;
         $depth--;
         //tree($n[1][1],$i3,$depth);
            $n[1][1][2]=$i3;
            if($depth){
               $i4=$i3+$i3;
               $depth--;
               tree($n[1][1][1],$i4,$depth);
               $i4--;
               tree($n[1][1][0],$i4,$depth);
               $depth++;
            }
         $i3--;
         //tree($n[1][0],$i3,$depth);
            $n[1][0][2]=$i3;
            if($depth){
               $i4=$i3+$i3;
               $depth--;
               tree($n[1][0][1],$i4,$depth);
               $i4--;
               tree($n[1][0][0],$i4,$depth);
               $depth++;
            }
         $depth++;
      }
   $i2--;
   //tree($n[0],$i2,$depth);
      $n[0][2]=$i2;
      if($depth){
         $i3=$i2+$i2;
         $depth--;
         //tree($n[0][1],$i3,$depth);
            $n[0][1][2]=$i3;
            if($depth){
               $i4=$i3+$i3;
               $depth--;
               tree($n[0][1][1],$i4,$depth);
               $i4--;
               tree($n[0][1][0],$i4,$depth);
               $depth++;
            }
         $i3--;
         //tree($n[0][0],$i3,$depth);
            $n[0][0][2]=$i3;
            if($depth){
               $i4=$i3+$i3;
               $depth--;
               tree($n[0][0][1],$i4,$depth);
               $i4--;
               tree($n[0][0][0],$i4,$depth);
               $depth++;
            }
         $depth++;
      }
   $depth++;
}

function check(&$n) {//checks look like free from a performance standpoint.
   $r=$n[2];
   if($n[0][0] === null){
      $r+=check($n[0]);
   }else{
      $r+=$n[0][2];
   }
   if($n[1][0] === null){
      $r-=check($n[1]);
   }else{
      $r-=$n[1][2];
   }
   return $r;
}

$threads=2;
// Currently limited to two threads, designed for 2n-threads, 
// can be updated to 4 threads when PHP releases a better array / memory limit
// on the benchmarks game goes up to at least 4.8G


$min_depth = 4;
if($argc == 2){
   $max_depth=$argv[1];
}else{
   $max_depth=$min_depth+2;
}
$stretch_depth=$max_depth+1;
$stree=array();
$i=0;
tree($stree,$i,$stretch_depth);
printf("stretch tree of depth %d\t check: %d\n",$stretch_depth, check($stree));
unset($stree);
$bigtree=array();
tree($bigtree,$i,$max_depth);
$runs = 1 << ($max_depth);


$tok = ftok(__FILE__, chr(time() & 255));
$queue = msg_get_queue($tok);

$parent = TRUE;
for ($i = 0; $i < $threads-1; ++$i) {
   $pid = pcntl_fork();
   if ($pid === -1) {
      die('could not fork');
   } else if ($pid) {
      continue;
   }
   $parent = FALSE;
   break;
}
if(!$parent){
   unset($bigtree);
}
$tmptree=array();
$return=array();
while($min_depth <= $max_depth){
   $check = 0;
   for($i=0;$i<$runs>>(2/$threads);$i++){
      tree($tmptree,$i,$min_depth);
      $check+=check($tmptree);
      $j=-$i;
      tree($tmptree,$j,$min_depth);
      $check+=check($tmptree);
   }
   $return[$min_depth]=array($runs,$check);
   $min_depth+=2;
   $runs>>=2;
}
unset($tmptree);
if (!msg_send($queue, 2, $return, TRUE, FALSE, $errno)) {
   var_dump("$errno");
   var_dump(msg_stat_queue($queue));
}

if (!$parent) {
   exit(0);
}

$result2=array();
for($i=0;$i<$threads;$i++){
   msg_receive($queue, 2, $msgtype, 2048, $result, TRUE);
   foreach($result as $depth => $res){
      $result2[$depth][0]+=$res[0];
      $result2[$depth][1]-=$res[1];
   }
   pcntl_wait($s);
}
foreach($result2 as $depth => $res){
   printf("%d\t trees of depth %d\t check: %d\n", $result2[$depth][0], $depth, -$result2[$depth][1]);
}
printf("long lived tree of depth %d\t check: %d\n",
$max_depth, check($bigtree));
