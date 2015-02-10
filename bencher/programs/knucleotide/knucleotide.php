<?
/* 
   The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Damien Bonvillain
   fixed by Isaac Gouy
   improved by Levi Cameron
   made about 8 times faster by Ludovic Urbain
   pcntl has a flat overhead of about 40ms on my VM, read_sequence takes about 10%
*/


function read_sequence($id) {
   $id = '>' . $id;
   $ln_id = strlen($id);
   $fd = STDIN;

   // reach sequence three
   
   while (strpos($line, $id) ===false){
      $line = stream_get_line($fd, 64, "\n"); // returns faster when the length is too large
      if(feof($fd)){
         exit(-1);
      }
   }
   // next, read the content of the sequence
   $r='';
   while (!feof($fd)) {
      $line = stream_get_line($fd, 64, "\n");
      if (!isset($line[0])) continue;
      $c = $line[0];
      if ($c === ';') continue;
      if ($c === '>') break;
      $r.=$line;
   }
   return strtoupper($r);
}

function write_freq(&$sequence,&$seqlen, &$keys) {
   $map = array();
   $total=0;
   if(strlen($keys[0])==1){
      foreach($keys as $p){
         $map[$p]=substr_count($sequence,$p);
         $total+=$map[$p];
      }
   }elseif(strlen($keys[0])==2){
      //this is here only because substr_count does not count overlapping strings,
      // it can be replaced by anything counting overlapping strings and with better performance
      for($i=0;$i<$seqlen-1;$i++){
         $map[$sequence[$i].$sequence[$i+1]]++;
      }
      foreach($keys as $p){
         $total+=$map[$p];
      }
   }
   foreach($keys as $p){
      $map[$p]=( $map[$p] / $total ) *100;
   }
   uasort($map, 'freq_name_comparator');
   $r='';
   foreach($map as $key => $val) {
      $r.=sprintf ("%s %.3f\n", $key, $val);
   }
   return $r."\n";
}

function write_count(&$sequence,&$seqlen, $key){
   return sprintf ("%d\t%s\n", substr_count($sequence,$key), $key);
}

function freq_name_comparator($a, $b) {
   if ($a == $b) return 0;
   return  ($a < $b) ? 1 : -1;
}
$sequence = read_sequence('THREE');
$seqlen=strlen($sequence);
fclose(STDIN);

$jobs = array(
   array('write_freq', array('A','T','G','C')),
   array('write_freq', array('AA','AT','TA','TT','CA','GA','AG','AC','TG','GT','TC','CT','GG','GC','CG','CC')),
   array('write_count', 'GGT'),
   array('write_count', 'GGTA'),
   array('write_count', 'GGTATT'),
   array('write_count', 'GGTATTTTAATT'),
   array('write_count', 'GGTATTTTAATTTATAGT')
);

$tok = ftok(__FILE__, chr(time() & 255));
$queue = msg_get_queue($tok);

$parent = TRUE;
$count = count($jobs);
for ($i = 0; $i < $count-1; ++$i) {
   $pid = pcntl_fork();
   if ($pid === -1) {
      die('could not fork');
   } else if ($pid) {
      continue;
   }
   $parent = FALSE;
   break;
}
$result = array($i,$jobs[$i][0]($sequence, $seqlen, $jobs[$i][1]));
if (!msg_send($queue, 2, $result, TRUE, FALSE, $errno)) {
   var_dump("$errno");
   var_dump(msg_stat_queue($queue));
}

if (!$parent) {
   exit(0);
}

$results = array();
foreach($jobs as $job) {
   msg_receive($queue, 2, $msgtype, 256, $result, TRUE);
   $results[$result[0]] = $result[1];
   pcntl_wait($s);
}

ksort($results);
foreach ($results as $result) {
   echo $result;
}

msg_remove_queue($queue);
