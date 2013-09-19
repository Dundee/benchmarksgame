<?php 
/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Peter Baltruschat
   modified by Levi Cameron
   modified by Craig Russell
*/

class Tree {
   public $i;
   public $l;
   public $r;
   
   public function __construct($item, $depth) {
      $this->i = $item;
      if($depth) {
         $this->l = new Tree($item * 2 - 1, --$depth);
         $this->r = new Tree($item * 2, $depth);
      }
   }
   
   public function check() {
      return $this->i
         + ($this->l->l === null ? $this->l->check() : $this->l->i)
         - ($this->r->l === null ? $this->r->check() : $this->r->i);
   }
}

$minDepth = 4;

$n = $argc == 2 ? $argv[1] : 1;
$maxDepth = $minDepth + 2 > $n ? $minDepth + 2 : $n;
$stretchDepth = $maxDepth + 1;

$stretch = new Tree(0, $stretchDepth);
printf("stretch tree of depth %d\t check: %d\n",
   $stretchDepth, $stretch->check());
unset($stretch);

$longLivedTree = new Tree(0, $maxDepth);

$iterations = 1 << $maxDepth;
do
{
   $check = 0;
   for($i = 1; $i <= $iterations; ++$i)
   {
      $check += (new Tree($i, $minDepth))->check()
         + (new Tree(-$i, $minDepth))->check();
   }
   
   printf("%d\t trees of depth %d\t check: %d\n",
      $iterations<<1, $minDepth, $check);
   
   $minDepth += 2;
   $iterations >>= 2;
}
while($minDepth <= $maxDepth);

printf("long lived tree of depth %d\t check: %d\n",
   $maxDepth, $longLivedTree->check());
