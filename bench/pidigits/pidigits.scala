/* 
 * The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 * contributed by Joseph Tang
*/

import math._

def pi(n: Int) = {
  val pr = 1.0 / pow(10, n)
  def t(initial: BigDecimal = 2, i: Int = 0): Int = {
    val current = (i + 1) * 1.0 / (2 * i + 3) * initial
    if (current < pr) i else t(current, i + 1)
  }
  val ts = t()
  def calPi(i: Int): BigDecimal = if (i > ts) 2.5 else 2 + i * calPi(i + 1) / (2 * i + 1)
  calPi(1)
}

def p10(s: String, offset: Int = 0) {
  val (s1, r) = s.splitAt(10)
  println(s"$s1\t:${offset + s1.length}")
  if (!r.isEmpty) p10(r, 10 + offset)
}

val n = if (args.isEmpty) 27 else args(0).toInt
p10(pi(n).toString().filterNot(_ == '.').substring(0, n))
