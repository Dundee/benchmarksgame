/*
 * The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 * contributed by Benedikt Nordhoff
 * port from revcomp.scala-5.scala (Rex Kerr)
 */

import scala.collection.mutable.ArrayBuffer

object revcomp {
  def hl(s: String) = s + s.toLowerCase
  val table:Map[Char,Char] = Map( (hl("ACGTUMRWSYKVHDBN") zip ("TGCAAKYWSRMBDHVN"*2)): _* )
  def mapChar(s:Char) = table(s)

  def main(args:Array[String]) = {
    var buf:ArrayBuffer[Char] = new ArrayBuffer
    var name = ""
    for (line <- io.Source.stdin.getLines) {
      if(line.startsWith(">")) {
        printResult(name,buf) // print the old stuff
        name = line
        buf = new ArrayBuffer 
      } else {
        buf ++= line
      }        
    }
    printResult(name,buf)
  }

  
  private var lastThread:Thread = null
  def printResult(name: String, buf:ArrayBuffer[Char]) {
    if(buf.isEmpty) return   
    val prev = lastThread
    lastThread = new Thread { 
      override def run() {
        buf.transform(mapChar) // do the mapping inplace
        val res = new ArrayBuffer[Array[Char]] // buffer for reverse grouped data
        for(x <- buf.reverseIterator.grouped(60)) {res += x.toArray} // fill buffer
        if(prev != null)
          prev.join() // join with previous thread before output
        println(name)
        for(x <- res){System.out.println(x)} // print char arrays 
      }
    }
    lastThread.start()
  }
}
