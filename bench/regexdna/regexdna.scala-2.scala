/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Contributed by The Anh Tran
   Updated for 2.8 by Rex Kerr
   Modified by Michael Peng for 2.10
*/

import scala.concurrent.duration.Duration
import java.util.regex.Pattern
import scala.concurrent._
import ExecutionContext.Implicits.global
import scala.io.Source

object regexdna {
  def main(args : Array[String]) {
    // load data from stdin
    var initInput = Source.stdin.mkString
    val init_len = initInput length

    // strip header & newline
    val input = ">.*\n|\n".r replaceAllIn(initInput, "")
    val strip_len = input length

    // counting patterns
    val patterns  = Seq(
      "agggtaaa|tttaccct" ,
      "[cgt]gggtaaa|tttaccc[acg]",
      "a[act]ggtaaa|tttacc[agt]t",
      "ag[act]gtaaa|tttac[agt]ct",
      "agg[act]taaa|ttta[agt]cct",
      "aggg[acg]aaa|ttt[cgt]ccct",
      "agggt[cgt]aa|tt[acg]accct",
      "agggta[cgt]a|t[acg]taccct",
      "agggtaa[cgt]|[acg]ttaccct")

    // queue tasks, each task is handled in a separate thread
    val count_results  = patterns map( pt =>
      future(
        (pt, pt.r.findAllIn(input).length)
      )
    )

    // replace IUB
    val iub = Map(
      "B" -> "(c|g|t)",
      "D" -> "(a|g|t)",
      "H" -> "(a|c|t)",
      "K" -> "(g|t)",
      "M" -> "(a|c)",
      "N" -> "(a|c|g|t)",
      "R" -> "(a|g)",
      "S" -> "(c|g)",
      "V" -> "(a|c|g)",
      "W" -> "(a|t)",
      "Y" -> "(c|t)")

    val replace_result  = {
      val buffer  = new StringBuffer((input.length * 3) / 2)
      val matcher  = Pattern compile "[BDHKMNRSVWY]" matcher input

      while ( matcher find )
        matcher appendReplacement( buffer, iub(matcher group))

      matcher appendTail buffer
      buffer length
    }

    // print results
    Await.result(Future.sequence(count_results), Duration.Inf) foreach (v => printf("%s %d\n", v._1, v._2))
    printf( "\n%d\n%d\n%d\n", init_len, strip_len, replace_result )
  }
}
