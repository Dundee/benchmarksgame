/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by KP
*/

package main

import (
   "fmt"
   "os"
   "runtime"
   "strconv"
   "sync"
)

type Token int

type T struct {
   next  *T
   label int
   value int
   mux   sync.Mutex
}

func (w *T) put(v int) {
   w.value = v
   if v == 0 {
      res <- w.label
   } else {
      w.mux.Unlock()
   }
}

func (w *T) run() {
   for {
      w.mux.Lock()
      w.next.put(w.value - 1)
      runtime.Gosched()
   }
}

func (w *T) Start(label int, next *T) {
   w.label = label
   w.next = next
   w.mux.Lock()
   go w.run()
}

const NThreads = 503

var res = make(chan int)

func main() {
   n := 1000
   if len(os.Args) > 1 {
      n, _ = strconv.Atoi(os.Args[1])
   }

   var channels [NThreads]T
   for i := range channels {
      channels[i].Start(i+1, &channels[(i+1)%NThreads])
   }

   channels[0].put(n)
   fmt.Println(<-res)
}
