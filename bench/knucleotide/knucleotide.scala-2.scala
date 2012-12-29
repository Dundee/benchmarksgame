/* The Computer Language Benchmarks Game
   http://shootout.alioth.debian.org/
   
   Contributed by Rex Kerr
   (inspired by the C++ version by Andrew Moon)
*/

import java.io._
import actors.Futures._

object knucleotide {
  val BlockSize = 1024*1024
  
  // Iterators are not specialized so we need our own
  abstract class LongIter {
    def hasNext: Boolean
    def next: Long
    def foreach(f: Long => Unit) { while (hasNext) f(next) }
  }
  
  def table(b: Byte) = (b >> 1) & 0x3
  /*Equivalent to: b match {
    case 'a' | 'A' => 0
    case 't' | 'T' => 2
    case 'g' | 'G' => 3
    case 'c' | 'C' => 1
  }*/

  // More efficient to store DNA sequence data as bits instead of bytes
  class Bits(maxSize: Int) {
    val data = new Array[Int](1+((maxSize+15) >> 4))
    var n, index = 0
    
    def entries = 16*index + (n >> 1)
    
    def add(i: Int) {
      if (n>30) { index += 1; n = 0 }
      data(index) |= i << n
      n += 2
    }
    
    def add(bs: Bits) {
      if (n==0 || n>30) {
        if (n>30) { index += 1; n = 0 }
        System.arraycopy(bs.data,0,data,index,bs.index)
        index += bs.index
        if (index > 0 && n == 0) { index -= 1; n = 32 }
      }
      else {
        var i = 0
        while (i < bs.index) {
          val j = bs.data(i)
          data(index) |= j << n
          index += 1
          data(index) |= j >>> (32-n)
          i += 1
        }
      }
      if (bs.n != 0) {
        var n = bs.n
        var i = bs.data(bs.index)
        while (n > 0) {
          add( i & 0x3 )
          i >>>= 2
          n -= 2
        }
      }
    }
    
    def scan(n: Int, offset: Int) = new LongIter {
      var i = offset & 0xF
      var j = offset >> 4
      var cache = data(j)
      val mask = (1L << (2*n)) - 1
      val limit = entries
      def hasNext = j*16 + i + n <= limit
      def next = {
        if (i+n <= 16) {
          val l = ((cache >>> (2*i)) & mask)
          i += n
          if (i>=16) { j += 1; cache = data(j); i -= 16 }
          l
        }
        else {
          var l = ((cache >>> (2*i))).toLong
          j += 1
          cache = data(j)
          l |= (cache.toLong << 2*(16-i)) & mask
          i += n - 16
          if (i>=16) { j += 1; cache = data(j); i -= 16 }
          l
        }
      }
    }
  }
  
  // Specialized line-by-line reading of UTF-8 input
  case class Line(data: Array[Byte], max: Int, var start: Int, var len: Int) {
    def apply(i: Int) = data(start+i)
    def getNext = {
      start += len
      len = 0
      while (start < max && data(start)=='\n') start += 1
      while (start+len < max && data(start+len)!='\n') len += 1
      len > 0 && (start+len < max || data.length != max)
    }
  }
  class LineReader(initial: Array[Byte], max0: Int) extends Iterator[Line] {
    private[this] var current = Line(initial,max0,0,0)
    // Must only call giveNext with more data after a failed hasNext or if max was zero
    def giveNext(another: Array[Byte], max: Int) = {
      val old = current
      current = new Line(another,max,0,0)
      if (old.len > 0) {
        Some( if ((max>0 && current(0)=='\n') || !current.getNext) old else {
          val a = new Array[Byte](old.len + current.len)
          System.arraycopy(old.data,old.start,a,0,old.len)
          System.arraycopy(current.data,0,a,old.len,current.len)
          Line(a,a.length,0,a.length)
        })
      }
      else if (current.getNext) Some(current)
      else None
    }
    // Warning--hasNext is destructive and can only be called once!
    def hasNext = current.getNext
    // Must only call after a successful hasNext
    def next = current
  }


  // Load a UTF-8 DNA file from standard in, picking out requested sequence
  def load(is: InputStream, target: Array[Byte]) = {
    var found,done = false
    val reader = new LineReader(Array(),0)
    var built = 0
    def read: Array[Bits] = {
      if (done) Array()
      else {
        val builder = Array.newBuilder[Bits]
        def process(l: Line) {
          if (!found) {
            var i = 0
            while (i<target.length && target(i)==l(i)) i += 1
            if (i==target.length) found = true
          }
          else if (!done) {
            if (l(0)=='>') done = true
            else {
              val b = new Bits(l.len)
              var i = 0
              while (i < l.len) {
                b.add( table(l(i)) )
                i += 1
              }
              builder += b
            }
          }
        }
        val data = new Array[Byte](BlockSize)
        val n = is.read(data)
        reader.giveNext(data,0 max n).foreach(l => process(l))
        while (reader.hasNext && !done) process(reader.next)
        if (n <= 0) done = true
        builder.result
      }
    }
      
    val data = Iterator.continually(read).takeWhile(_ => !done).toArray.flatten
    val all = new Bits(data.map(_.entries).sum)
    data.foreach(all.add)
    all
  }
  
  // Utility to go from binary to text representation
  val decode = Map(0L->"A", 2L->"T", 3L->"G", 1L->"C")
  def l2s(l: Long, n: Int): String = {
    if (n <= 0) ""
    else decode(l&0x3) + l2s(l>>>2, n-1)
  }
    
  // Custom counted primitive hash set (neither Java nor Scala provides one)
  class DnaHash(z: Int) {
    var size = 16
    var n = 0
    var keys = new Array[Long](size)
    var counts = new Array[Int](size)
    final def hash(l: Long) = (l.toInt + (l>>17).toInt) & (size-1)
    final def inc(i: Int) = (i+1) & (size - 1)
    def +=(key: Long, count: Int = 1) {
      val index = hash(key)
      if (counts(index) == 0) {
        keys(index) = key
        counts(index) = count
        n += 1
      }
      else if (keys(index) == key) counts(index) += count
      else if (6*n > size) {
        val (oldk, oldc, olds) = (keys, counts, size)
        size *= 2
        keys = new Array[Long](size)
        counts = new Array[Int](size)
        n = 0
        var i = 0
        while (i < olds) {
          if (oldc(i) > 0) this += (oldk(i), oldc(i))
          i += 1
        }
        this += key
      }
      else {
        var i = inc(index)
        while (counts(i) != 0 && keys(i) != key) i = inc(i)
        if (counts(i) == 0) {
          keys(i) = key
          counts(i) = count
          n += 1
        }
        else counts(i) += count
      }
    }
    def apply(key: Long) = {
      var i = hash(key)
      while (counts(i) > 0 && keys(i) != key) i = inc(i)
      counts(i)
    }
    def printSorted {
      val factor = 100.0/counts.sum
      (counts.map(_*factor) zip keys.map(l2s(_,z))).filter(_._1 > 0).sortWith((a,b) =>
        a._1 > b._1 || (a._1 == b._1 && a._2 < b._2)
      ).foreach{ case (freq, label) => printf("%s %.3f\n",label,freq) }
      println
    }
    def print(s: String) {
      val key = s.getBytes.map(x => table(x).toLong).reduceRight((l,r) => 4*r + l)
      printf("%d\t%s\n",this(key),s)
    }
  }
  
  // Required function that adds data with offset to hash set
  def addToHash(data: Bits, hash: DnaHash, n: Int, offset: Int) = data.scan(n,offset).foreach(hash += _)
  
  def main(args: Array[String]) {
    val sizes = List(1,2,3,4,6,12,18)
    val sequence = "GGTATTTTAATTTATAGT"
    val data = load(System.in, ">THREE".getBytes)
    val answers = sizes.map(n => n -> future {
      val h = new DnaHash(n)
      for (i <- 0 until n) addToHash(data,h,n,i)
      h
    }).toMap
    answers(1)().printSorted
    answers(2)().printSorted
    sizes.drop(2).foreach(n => answers(n)().print(sequence.substring(0,n)))
  }
}
