/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/
   Based partially on the version by The Anh Tran
   Based on bit encoding idea of C++ contribution of Andrew Moon
   Contributed by Mark Hammons
   Updated for 2.10.0 by Mark Hammons
*/

import akka.actor.ActorSystem
import annotation.tailrec
import java.util.HashMap
import java.io.{InputStreamReader, BufferedReader}

import concurrent.{Await, ExecutionContext, Future}
import concurrent.duration.Duration


object knucleotide extends App {

  val as = ActorSystem.create("futures")

  implicit val ec: ExecutionContext = as.dispatcher

  val reader = new BufferedReader(new InputStreamReader(System.in,  "US-ASCII"), 8*1024*1024)

  var buffer = "lola"

  while(buffer != null && (buffer(0) != '>' || buffer(2) != 'H'))
    buffer = reader.readLine();

  type mapSeqType = List[(Future[NucleoHashMap],(String,Int))]

  val digestibleSeq:mapSeqType = List(1,2,3,4,6,12,18).map(frame => Future(new NucleoHashMap(frame)) -> ("",frame))

  @tailrec
  def digester(maps: mapSeqType, length: Int = 0): (mapSeqType,Int) = {
    var in = new StringBuilder

    while(reader.ready() && in.length < 500000)
      in.append(reader.readLine())

    val input = in.stripLineEnd.toLowerCase

    val f = maps.map(x => Await.result(x._1, Duration.Inf) -> x._2)

    def getRemainder(str: String, frame: Int) = str.substring(str.length - (frame-1))
    val res = for((map,(remainder,frame)) <- f) yield
      Future{
        val in = remainder + input
        map.process(in)
        map
      } -> (getRemainder(remainder+input,frame),frame)


    if(reader.ready) digester(res,input.length + length)
    else (res,(input.length + length))
  }

  val (retList,length) = digester(digestibleSeq)

  val maps = retList.map(r => Await.result(r._1, Duration.Inf))

  def lookup(str: String): Int = {
    val l = str.length
    val map = maps.find(_.frame == l).getOrElse(throw new Exception("no keys of length " + l))
    map(str)
  }

  val componentList = List("a","t","c","g")
  val singles = for(i <- componentList)
    yield (i).toUpperCase -> (lookup(i)/length.toDouble * 100)

  val doubles = for(i <- componentList; j <- componentList)
    yield (i+j).toUpperCase -> (lookup(i+j)/length.toDouble * 100)

  singles.sortWith(_._2 > _._2).foreach(a => {printf("%s %.3f\n",a._1,a._2);})
  println("")
  doubles.sortWith(_._2 > _._2).foreach(a => {printf("%s %.3f\n",a._1,a._2);})

  println("")

  List("GGT","GGTA","GGTATT", "GGTATTTTAATT", "GGTATTTTAATTTATAGT").foreach(
    s => println(lookup(s.toLowerCase) + "\t" + s)
  )

  as.shutdown()
}

class NucleoHashMap(val frame: Int)(implicit ec: ExecutionContext) {
  class BabyHash[K] extends HashMap[K,Count] {
  @inline final def apply(key: K): Count = {
      val r = this.get(key)
      if(r != null)
        r
      else {
        val c = new Count
        put(key,c)
        c
      }
    }
  }

  val maps = for(i <- 0 until frame)
    yield new BabyHash[String] -> i

  def process(str: String) {
    @inline
    def addStrings(map: BabyHash[String], str: String) {
      var offset = 0
      var frmoffset = offset + frame
      while(str.length >= frmoffset) {
        map(str.substring(offset,frmoffset)) += 1
        offset += frame
        frmoffset += frame
      }
    }

    maps.map(x => Future{addStrings(x._1,str.substring(x._2))}).map(Await.result(_, Duration.Inf))
  }

  def apply(str: String):Int = {
    maps.foldLeft(0)((z,map) => map._1(str)+z)
  }
}

class Count {
  var count = 0

  @inline final def +=(v: Int) {count += v}

  @inline final def +(v: Int) = count + v
}
 
