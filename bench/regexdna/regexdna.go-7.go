/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 *
 * contributed by The Go Authors.
 * modified by Glenn Brown.
 */

package main

import (
   "fmt"
   "github.com/glenn-brown/golang-pkg-pcre/src/pkg/pcre"
   "io/ioutil"
   "os"
)

var variants = []string{
   "agggtaaa|tttaccct",
   "[cgt]gggtaaa|tttaccc[acg]",
   "a[act]ggtaaa|tttacc[agt]t",
   "ag[act]gtaaa|tttac[agt]ct",
   "agg[act]taaa|ttta[agt]cct",
   "aggg[acg]aaa|ttt[cgt]ccct",
   "agggt[cgt]aa|tt[acg]accct",
   "agggta[cgt]a|t[acg]taccct",
   "agggtaa[cgt]|[acg]ttaccct",
}

type Subst struct {
   pat, repl string
}

var substs = []Subst{
   Subst{"B", "(c|g|t)"},
   Subst{"D", "(a|g|t)"},
   Subst{"H", "(a|c|t)"},
   Subst{"K", "(g|t)"},
   Subst{"M", "(a|c)"},
   Subst{"N", "(a|c|g|t)"},
   Subst{"R", "(a|g)"},
   Subst{"S", "(c|g)"},
   Subst{"V", "(a|c|g)"},
   Subst{"W", "(a|t)"},
   Subst{"Y", "(c|t)"},
}

func countMatches(pat string, bytes []byte) int {
   re := pcre.MustCompile(pat, 0)
   n := 0
   for {
      e := re.FindIndex(bytes, 0)
      if len(e) == 0 {
         break
      }
      n++
      bytes = bytes[e[1]:]
   }
   return n
}

func main() {
   bytes, err := ioutil.ReadAll(os.Stdin)
   if err != nil {
      fmt.Fprintf(os.Stderr, "can't read input: %s\n", err)
      os.Exit(2)
   }
   ilen := len(bytes)
   // Delete the comment lines and newlines
   bytes = pcre.MustCompile("(>[^\n]+)?\n", 0).
      ReplaceAll(bytes, []byte{}, 0)
   clen := len(bytes)
   for _, s := range variants {
      fmt.Printf("%s %d\n", s, countMatches(s, bytes))
   }
   for _, sub := range substs {
      bytes = pcre.MustCompile(sub.pat, 0).
         ReplaceAll(bytes, []byte(sub.repl), 0)
   }
   fmt.Printf("\n%d\n%d\n%d\n", ilen, clen, len(bytes))
}
