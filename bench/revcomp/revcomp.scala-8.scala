/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/
 *
 * contributed by Erik Osheim
 */

import java.util.concurrent.ForkJoinPool
import java.util.concurrent.RecursiveAction

import scala.collection.mutable
import scala.annotation.tailrec

final object revcomp {
  final val pool: ForkJoinPool = new ForkJoinPool()
  final val bytes: Array[Byte] = new Array[Byte](128)

  final def init() {
    val a = "ACBDGHK\nMNSRUTWVYacbdghkmnsrutwvy"
    val b = "TGVHCDM\nKNSYAAWBRTGVHCDMKNSYAAWBR"
    var i = 0
    while (i < a.length) { bytes(a.charAt(i)) = b.charAt(i).toByte; i += 1 }
  }

  init()

  final case class Reverse(buf: Array[Byte], begin: Int, end: Int) extends RecursiveAction {
    final def compute() {
      var buf = this.buf
      var begin = this.begin
      var end = this.end
      while (true) {
        var bb = buf(begin)
        if (bb == '\n') { begin += 1; bb = buf(begin) }
        var be = buf(end)
        if (be == '\n') { end -= 1; be = buf(end) }
        if (begin > end) return ()
        buf(begin) = be
        begin += 1
        buf(end) = bb
        end -= 1
      }
    }
  }

  @inline @tailrec def inner(i: Int, len: Int, buf: Array[Byte]): Int = if (i < len) {
    val b = buf(i)
    if (b != '>') {
      buf(i) = bytes(b)
      inner(i + 1, len, buf)
    } else {
      i + 1
    }
  } else {
    i
  }

  final def main(args: Array[String]) {
    val len = System.in.available
    val buf = new Array[Byte](len)
    System.in.read(buf)
    val tasks = mutable.ListBuffer.empty[Reverse]

    var i = 0
    while (i < len) {
      while (buf(i) != '\n') i += 1
      val j = inner(i, len, buf)
      val task = new Reverse(buf, i, j - 2)
      pool.execute(task)
      tasks.append(task)
      i = j + 1
    }

    var last = 0
    tasks.foreach { t =>
      t.join()
      System.out.write(buf, last, t.end - last)
      last = t.end
    }
    System.out.write(buf, last, len - last)
  }
}
