/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 *
 * contributed by The Go Authors.
 * based on C program by Kevin Carson
 * flag.Arg hack by Isaac Gouy
 * slice based binary tree by Jakob Borg
 * channel based parallellism by Jakob Borg
 */

package main

import (
   "flag"
   "fmt"
   "runtime"
   "strconv"
)

var n = 0

type Node struct {
   value       int
   hasChildren bool
}

type Tree []Node

func bottomUpTree(item, depth int) Tree {
   t := make(Tree, 1<<uint(depth+1))
   t.setNodes(0, item, depth)
   return t
}

func (t Tree) setNodes(idx, item, depth int) {
   t[idx].value = item
   if depth > 0 {
      t[idx].hasChildren = true
      t.setNodes(2*idx+1, 2*item-1, depth-1)
      t.setNodes(2*idx+2, 2*item, depth-1)
   }
}

func (t Tree) itemCheck() int {
   return t.itemCheckIdx(0)
}

func (t Tree) itemCheckIdx(idx int) int {
   if !t[idx].hasChildren {
      return t[idx].value
   }
   return t[idx].value + t.itemCheckIdx(2*idx+1) - t.itemCheckIdx(2*idx+2)
}

const minDepth = 4

func main() {
   var parallell = runtime.NumCPU() * 2
   runtime.GOMAXPROCS(parallell)

   flag.Parse()
   if flag.NArg() > 0 {
      n, _ = strconv.Atoi(flag.Arg(0))
   }

   maxDepth := n
   if minDepth+2 > n {
      maxDepth = minDepth + 2
   }
   stretchDepth := maxDepth + 1

   check := bottomUpTree(0, stretchDepth).itemCheck()
   fmt.Printf("stretch tree of depth %d\t check: %d\n", stretchDepth, check)

   longLivedTree := bottomUpTree(0, maxDepth)

   var res = make([]chan int, parallell)
   for j := 0; j < parallell; j++ {
      res[j] = make(chan int)
   }

   for depth := minDepth; depth <= maxDepth; depth += 2 {
      iterations := 1 << uint(maxDepth-depth+minDepth)

      for j := 0; j < parallell; j++ {
         go func(j int) {
            c := 0
            for i := j; i <= iterations; i += parallell {
               c += bottomUpTree(i, depth).itemCheck()
               c += bottomUpTree(-i, depth).itemCheck()
            }
            res[j] <- c
         }(j)
      }

      check = 0
      for j := 0; j < parallell; j++ {
         check += <-res[j]
      }

      fmt.Printf("%d\t trees of depth %d\t check: %d\n", iterations*2, depth, check)
   }
   fmt.Printf("long lived tree of depth %d\t check: %d\n", maxDepth, longLivedTree.itemCheck())
}
