/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Contributed by Marceli Narcyz
*/

import java.io.OutputStream

object fastaredux {
  val LINE_LENGTH: Int = 60;
  val OUT_BUFFER_SIZE: Int = 256 * 1024;
  val LOOKUP_SIZE: Int = 4 * 1024;
  val LOOKUP_SCALE: Double = LOOKUP_SIZE - 1;

  case class Freq(var c: Byte, var p: Double);

  val ALU =
    "GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG" +
      "GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA" +
      "CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT" +
      "ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA" +
      "GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG" +
      "AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC" +
      "AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA";

  val IUB: Array[Freq] = Array[Freq](
    Freq('a', 0.27),
    Freq('c', 0.12),
    Freq('g', 0.12),
    Freq('t', 0.27),
    Freq('B', 0.02),
    Freq('D', 0.02),
    Freq('H', 0.02),
    Freq('K', 0.02),
    Freq('M', 0.02),
    Freq('N', 0.02),
    Freq('R', 0.02),
    Freq('S', 0.02),
    Freq('V', 0.02),
    Freq('W', 0.02),
    Freq('Y', 0.02));

  val HomoSapiens: Array[Freq] = Array[Freq](
    Freq('a', 0.3029549426680),
    Freq('c', 0.1979883004921),
    Freq('g', 0.1975473066391),
    Freq('t', 0.3015094502008));

  def sumAndScale(a: Array[Freq]): Unit = {
    var p: Double = 0;
    var i: Int = 0;
    while (i < a.length) {
      p = p + a(i).p
      a(i).p = p * LOOKUP_SCALE;
      i = i + 1;
    }
    a(a.length - 1).p = LOOKUP_SCALE;
  }

  object Random {
    val IM: Int = 139968;
    val IA: Int = 3877;
    val IC: Int = 29573;
    val SCALE: Double = LOOKUP_SCALE / IM;
    var last: Int = 42;

    def next(): Double = {
      last = (last * IA + IC) % IM

      SCALE * last;
    }
  }

  object Out {
    val buf = new Array[Byte](OUT_BUFFER_SIZE);
    val lim: Int = OUT_BUFFER_SIZE - 2 * LINE_LENGTH - 1;
    var ct: Int = 0;
    var stream: OutputStream = null;

    def checkFlush(): Unit = {
      if (ct >= lim) {
        stream.write(buf, 0, ct);
        ct = 0;
      }
    }

    def close(): Unit = {
      stream.write(buf, 0, ct);
      ct = 0;
      stream.close();
    }
  }

  object RandomFasta {
    val lookup: Array[Freq] = new Array[Freq](LOOKUP_SIZE);

    def makeLookup(a: Array[Freq]): Unit = {
      var i: Int = 0;
      var j: Int = 0;
      while (i < LOOKUP_SIZE) {
        while (a(j).p < i) {
          j = j + 1;
        }
        lookup(i) = a(j);
        i = i + 1;
      }
    }

    def addLine(bytes: Int): Unit = {
      Out.checkFlush();
      var lct: Int = Out.ct;
      while (lct < Out.ct + bytes) {
        val r: Double = Random.next();
        var ai: Int = r.asInstanceOf[Int];
        while (lookup(ai).p < r) {
          ai = ai + 1;
        }
        Out.buf(lct) = lookup(ai).c;
        lct = lct + 1;
      }
      Out.buf(lct) = '\n'.asInstanceOf[Byte];
      lct = lct + 1;
      Out.ct = lct;
    }

    def make(desc: String, a: Array[Freq], startN: Int): Unit = {
      var n = startN;
      makeLookup(a);

      System.arraycopy(desc.getBytes(), 0, Out.buf, Out.ct, desc.length());
      Out.ct += desc.length();

      while (n > 0) {
        val bytes: Int = Math.min(LINE_LENGTH, n);
        addLine(bytes);
        n = n - bytes;
      }
    }
  }

  object RepeatFasta {
    def make(desc: String, alu: Array[Byte], startN: Int): Unit = {
      var n = startN;
      System.arraycopy(desc.getBytes(), 0, Out.buf, Out.ct, desc.length());
      Out.ct += desc.length();

      val buf = new Array[Byte](alu.length + LINE_LENGTH);
      var i: Int = 0;
      while (i < buf.length) {
        System.arraycopy(alu, 0, buf, i, Math.min(alu.length, buf.length - i));
        i += alu.length;
      }

      var pos: Int = 0;
      while (n > 0) {
        val bytes: Int = Math.min(LINE_LENGTH, n);
        Out.checkFlush();
        System.arraycopy(buf, pos, Out.buf, Out.ct, bytes); Out.ct += bytes;
        Out.buf(Out.ct) = '\n'.asInstanceOf[Byte];
        Out.ct += 1
        pos = (pos + bytes) % alu.length;
        n -= bytes;
      }
    }
  }

  def main(args: Array[String]): Unit = {
    val n = (if (args.length > 0) Integer.parseInt(args(0)) else 2500000);

    sumAndScale(IUB);
    sumAndScale(HomoSapiens);

    Out.stream = System.out;
    RepeatFasta.make(">ONE Homo sapiens alu\n", ALU.getBytes(), n * 2);
    RandomFasta.make(">TWO IUB ambiguity codes\n", IUB, n * 3);
    RandomFasta.make(">THREE Homo sapiens frequency\n", HomoSapiens, n * 5);
    Out.close();
  }
}
