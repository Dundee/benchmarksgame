/*
 * The Computer Language Benchmarks Game 
 * http://benchmarksgame.alioth.debian.org/
 * contributed by Benedikt Nordhoff
 * parallelized adaptation of Scala version #4 by Rex Kerr (Java version #4 by Anthony Donnefort)
 */

import akka.actor._
object revcomp extends App {  
  val table = new Array[Byte](128)
  for (i <- 0 to 127) { table(i) = i.toByte }
  for ((i,o) <- "ACGTUMRWSYKVHDB".toList zip "TGCAAKYWSRMBDHVN".toList) {
    table(i) = o.toByte
    table(i.toLower) = o.toByte
  }
  val system = ActorSystem.create("MySystem");
  val mainActor = system.actorOf(Props[Reader])
}

//Messages
case class Parse(ar: Array[Byte],len:Int)
case class Part(ar:Array[Byte],start:Int,len:Int)
case object Reverse
case object Done
case object Write
case class Next(ref: ActorRef)

// Reads from stdio and forwards to Parser
class Reader extends Actor {
    override def preStart{
    val parser = context.actorOf(Props[Parser])
    val SIZE = 8192*10000    
    var n = 0
    do {
      val input = new Array[Byte](SIZE)
      n = System.in.read(input)
      if (n > 0) {
        parser ! Parse(input,n)        
      }
    } while (n != -1)
    parser ! Done
  }
  def receive = {case _ =>}
}

// Receives chunks from reader, looks for '>' and forwards chunks to Reverser
class Parser extends Actor {
  var cr:ActorRef = null
  def receive = {
    case Parse(ar,len) => 
      var i = 0
        var i0 = 0
        while (i < len) {
          if (ar(i)=='>') {
            if (i>i0) cr ! Part(ar,i0,i-i0)
            val nr = context.actorOf(Props[Reverser])
            if(cr != null) {
              cr ! Reverse
              cr ! Next(nr)
            } else {nr ! Write}
            cr = nr
            i0 = i
          }
          i += 1
        }
        if (i0<len) cr ! Part(ar,i0,len-i0)
    case Done => cr ! Reverse; cr ! Next(self)
    case Write => context.system.shutdown
  }
}

// Puts chucks together and reverses stuff
class Reverser extends java.io.ByteArrayOutputStream with Actor {
  var next:ActorRef = null
  var done = false
  var wdone = false
  def receive = {
    case Part(ar,start,len) => write(ar,start,len)
    case Reverse => doReverse
    case Write => if(done) doWrite else {done = true}
    case Next(ref) => next = ref; if(wdone) {next ! Write;context.stop(self)} else {wdone = true}
  }
  def doReverse {
    if (count > 0) {
      val table = revcomp.table
      var begin = 0
      var end = count-1
      while (buf(begin) != '\n' && begin < count) { begin += 1 }
      while (begin <= end) {
        if (buf(begin) == '\n') begin += 1
        if (buf(end) == '\n') end -= 1
        if (begin<=end) {
          val temp = buf(begin)
          buf(begin) = table(buf(end))
          buf(end) = table(temp)
          begin += 1
          end -= 1
        }
      }
      if(done) doWrite else done = true
    }
  }
  def doWrite {
    System.out.write(buf,0,count)
    if(wdone) {next ! Write;context.stop(self)} else wdone = true
  }    
} 
