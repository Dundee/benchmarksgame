/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 *
 * contributed by The Go Authors.
 * based on C program by Kevin Carson
 * flag.Arg hack by Isaac Gouy
 * custom pool and parallel loops by JONNALAGADDA Srinivas
 * removed channels, redid loops, use slice storage by Alex Skinner
*/

package main

import (
   "flag"
   "fmt"
   "runtime"
   "strconv"
   "sync"
)

type Node struct {
   i        int
   left, right *Node
}

type NodeStore struct {
   brk   int
   idx   int
   store []Node
}

func (s *NodeStore) Init(depth int) {
   s.brk = 1 << uint(depth+1)
   s.idx = -1
   s.store = make([]Node, s.brk)
}

func (s *NodeStore) ReInit() {
   s.idx = -1
}

func (s *NodeStore) Alloc(i int, l, r *Node) *Node {
   s.idx++
   p := &(s.store[s.idx])
   (*p).i = i
   (*p).left = l
   (*p).right = r
   return p
}

func iterTree(i, depth int, store *NodeStore) *Node {
   if depth <= 0 {
      return store.Alloc(i, nil, nil)
   }
   return store.Alloc(i,
      iterTree(2*i-1, depth-1, store),
      iterTree(2*i, depth-1, store))
}

func (n *Node) nodeCheck() int {
   if n.left == nil {
      return n.i
   }
   return n.i + n.left.nodeCheck() - n.right.nodeCheck()
}

func goIterate(d int, iterations int, arr []int, s *sync.WaitGroup, nss []*NodeStore) {
      c := 0
      x := new(NodeStore)
      x.Init(d)
      nss[d] = x
      for i:=1; i <= iterations; i++ {
          x.ReInit()
          c += iterTree(i,d,x).nodeCheck()
          x.ReInit()
          c += iterTree(-i,d,x).nodeCheck()
      }
      arr[d] = c
      arr[d+1] = iterations
      s.Done()
} 
func main() {
   var s sync.WaitGroup
   runtime.GOMAXPROCS(4)
   minDepth, n := 4,0
   flag.Parse()
   if flag.NArg() > 0 {
      n, _ = strconv.Atoi(flag.Arg(0))
   }
   maxDepth := n
   if n < 6 {
      maxDepth = 6
   }
   stretchDepth := maxDepth + 1
   store := &NodeStore{}
   store.Init(stretchDepth)
   curr := iterTree(0, stretchDepth, store)
   fmt.Printf("stretch tree of depth %d\t check: %d\n", stretchDepth, curr.nodeCheck())
   store.ReInit()
   longLivedTree := iterTree(0, maxDepth, store)
   arr := make([]int, stretchDepth+1)
   iterations := 0
   nss := make([]*NodeStore, stretchDepth)
   for d := minDepth; d <= maxDepth; d += 2 {
      iterations = 1 << uint8(maxDepth - d + minDepth)
      s.Add(1)
      go goIterate(d, iterations, arr, &s, nss)
   }
   s.Wait()
   for x := minDepth; x <= maxDepth; x += 2 {
      fmt.Printf("%d\t trees of depth %d\t check: %d\n", 2 * arr[x+1], x, arr[x])
   }    
   fmt.Printf("long lived tree of depth %d\t check: %d\n", maxDepth, longLivedTree.nodeCheck())
}
