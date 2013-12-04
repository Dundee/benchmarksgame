/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Contributed by Marceli Narcyz
*/

import scala.concurrent._
import scala.concurrent.duration._
import java.io.BufferedReader
import java.util.concurrent.Executors
import java.io.InputStreamReader
import java.nio.charset.Charset
import java.util.regex.Pattern

object regexdna {
  val newSeqChars = "BDHKMNRSVWY".toCharArray();

  val newSeqStrings = Array[String](
    "(c|g|t)", // B
    "(a|g|t)", // D
    "(a|c|t)", // H
    "(g|t)", // K
    "(a|c)", // M
    "(a|c|g|t)", // N
    "(a|g)", // R
    "(c|g)", // S
    "(a|c|g)", // V
    "(a|t)", // W
    "(c|t)" // Y
    );

  val variants = Seq(
    "agggtaaa|tttaccct",
    "[cgt]gggtaaa|tttaccc[acg]",
    "a[act]ggtaaa|tttacc[agt]t",
    "ag[act]gtaaa|tttac[agt]ct",
    "agg[act]taaa|ttta[agt]cct",
    "aggg[acg]aaa|ttt[cgt]ccct",
    "agggt[cgt]aa|tt[acg]accct",
    "agggta[cgt]a|t[acg]taccct",
    "agggtaa[cgt]|[acg]ttaccct")

  private def readInput(): (Int, String) = {
    val sb = new StringBuilder(10000000);
    val r = new BufferedReader(new InputStreamReader(System.in, Charset.defaultCharset()));

    var commentLength: Int = 0;
    try {
      var line: String = r.readLine();
      while (line != null) {
        if (line.charAt(0) == '>') {
          commentLength += line.length() + 1;
        } else {
          sb.append(line);
          commentLength += 1;
        }
        line = r.readLine();
      }
    } finally {
      r.close();
    }

    val result = sb.toString();
    return (result.length + commentLength, result);
  }

  def calcNewSeqLength(sequence: String): Int = {
    val seqChars = sequence.toCharArray();
    var acc: Int = 0;
    var seqI: Int = 0;
    while (seqI < seqChars.length) {
      val c = seqChars(seqI)
      val i = java.util.Arrays.binarySearch(newSeqChars, c);
      acc += (if (i >= 0) newSeqStrings(i).length() else 1);
      seqI += 1;
    }
    return acc;
  }

  def countVariantOccurences(sequence: String, variant: String): Int = {
	var count: Int = 0;
	val m = Pattern.compile(variant).matcher(sequence);
	while (m.find()) {
		count += 1;
	}
	count;
  }
  
  def slowCountVariantOccurences(sequence: String, variant: String): Int = sequence.r.findAllIn(sequence).length

  def main(args: Array[String]): Unit = {
    val es = Executors.newFixedThreadPool(variants.length + 1);
    implicit val ec = ExecutionContext.fromExecutorService(es);

    val (inputLength, sequence) = readInput();

    val newSeqLengthFuture = future { calcNewSeqLength(sequence) }
    val futures = variants.map(v => future { countVariantOccurences(sequence, v) })

    val duration = Duration(999, HOURS)
    val newSeqLength = Await.result(newSeqLengthFuture, duration);
    val counts = futures.map(f => Await.result(f, duration))

    es.shutdown();

    for (t <- variants zip counts) {
      System.out.println(t._1 + " " + t._2);
    }
    System.out.println();
    System.out.println(inputLength);
    System.out.println(sequence.length);
    System.out.println(newSeqLength);
  }
}
