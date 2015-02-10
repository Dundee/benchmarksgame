<?php 
/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Peter Baltruschat
   modified by Levi Cameron
*/
// see 

class Node {
	public $item;
	public $l;
	public $r;
}

function bottomUpTree($item, $depth)
{
   $node = new Node();
   $node->item = $item;
   if (!$depth) return $node;
   $item2 = $item + $item;
   $depth--;
   $node->l = bottomUpTree($item2-1,$depth);
   $node->r = bottomUpTree($item2,$depth);
   return $node;
}

function itemCheck($treeNode) { 
   return $treeNode->item
      + ($treeNode->l->l === null ? itemCheck($treeNode->l) : $treeNode->l->item)
      - ($treeNode->r->l === null ? itemCheck($treeNode->r) : $treeNode->r->item);
}

$minDepth = 4;

$n = ($argc == 2) ? $argv[1] : 1;
$maxDepth = max($minDepth + 2, $n);
$stretchDepth = $maxDepth + 1;

$stretchTree = bottomUpTree(0, $stretchDepth);
printf("stretch tree of depth %d\t check: %d\n", $stretchDepth, itemCheck($stretchTree));
unset($stretchTree);

$longLivedTree = bottomUpTree(0, $maxDepth);

$iterations = 1 << ($maxDepth);
do
{
   $check = 0;
   for($i = 1; $i <= $iterations; ++$i)
   {
      $t = bottomUpTree($i, $minDepth);
      $check += itemCheck($t);
      unset($t);
      $t = bottomUpTree(-$i, $minDepth);
      $check += itemCheck($t);
      unset($t);
   }
   
   printf("%d\t trees of depth %d\t check: %d\n", $iterations<<1, $minDepth, $check);
   
   $minDepth += 2;
   $iterations >>= 2;
}
while($minDepth <= $maxDepth);

printf("long lived tree of depth %d\t check: %d\n",
$maxDepth, itemCheck($longLivedTree));

