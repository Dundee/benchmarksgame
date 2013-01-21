/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/
  based on original contributed by Isaac Gouy
  updated for 2.9 and optimized by Rex Kerr
*/

import java.io._

object fasta {
  val ALU =
   ("GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG" +
    "GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA" +
    "CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT" +
    "ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA" +
    "GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG" +
    "AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC" +
    "AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA").getBytes

  val IUB = (
    "acgtBDHKMNRSVWY".getBytes,
    (Array(0.27,0.12,0.12,0.27) ++ Array.fill(11)(0.02)).scanLeft(0d)(_+_).tail
  )
  val HomoSapiens = (
    "acgt".getBytes,
    Array(0.3029549426680, 0.1979883004921, 0.1975473066391, 0.3015094502008).
      scanLeft(0d)(_+_).tail
  )

  def main(args: Array[String]) = {
    val n = args(0).toInt
    val s = new FastaOutputStream(System.out)

    s.writeDescription("ONE Homo sapiens alu")
    s.writeRepeating(ALU,n*2)

    s.writeDescription("TWO IUB ambiguity codes")
    s.writeRandom(IUB,n*3)

    s.writeDescription("THREE Homo sapiens frequency")
    s.writeRandom(HomoSapiens,n*5)

    s.close
  }
}


// Extend the Java BufferedOutputStream class

class FastaOutputStream(out: OutputStream) extends BufferedOutputStream(out) {
  private final val LineLength = 60
  private final val nl = '\n'.toByte

  def writeDescription(desc: String) = { write( (">" + desc + "\n").getBytes ) }

  def writeRepeating(alu: Array[Byte], length: Int) = {
    var n = length
    var k = 0
    val kn = alu.length;

    while (n > 0) {
      val m = if (n < LineLength) n else LineLength

      var i = 0
      while (i < m){
        if (k == kn) k = 0
        val b = alu(k)
        if (count < buf.length){ buf(count) = b; count += 1 }
        else { write(b) } // flush buffer
        k += 1
        i += 1
      }

      write(nl)
      n -= LineLength
    }
  }

  def writeRandom(distribution: (Array[Byte], Array[Double]), length: Int) = {
    val (bytes, cuml) = distribution
    var n = length
    while (n > 0) {
      val m = if (n < LineLength) n else LineLength

      var i = 0
      while (i < m){
        val b = bytes(selectRandom(cuml))
        if (count < buf.length) { buf(count) = b; count += 1 }
        else { write(b) } // flush buffer
        i += 1
      }

      if (count < buf.length){ buf(count) = nl; count += 1 }
      else { write(nl) } // flush buffer
      n -= LineLength
    }
  }

  private final def selectRandom(cuml: Array[Double]): Int = {
    val r = randomTo(1.0)
    var i = 0
    while (i < cuml.length) {
      if (r < cuml(i)) return i
      i += 1
    }
    i
  }
  
  private final val IM = 139968
  private final val IA = 3877
  private final val IC = 29573
  private final val IMinv = 1.0/IM
  private var seed = 42

  private final def randomTo(max: Double) = {
    seed = (seed * IA + IC) % IM
    max * seed * IMinv
  }
}
