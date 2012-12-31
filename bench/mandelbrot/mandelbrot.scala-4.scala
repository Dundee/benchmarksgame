/* The Computer Language Benchmarks Game
 * http://benchmarksgame.alioth.debian.org/
 *
 * contributed by Benedikt Nordhoff
 * direct translation from Java #2 by Stefan Krause et al.
 */

import java.io._
import java.util.concurrent.atomic._

object mandelbrot {
  var out:Array[Array[Byte]] = null
  var yCt:AtomicInteger = null
  var Crb:Array[Double] = null
  var Cib:Array[Double] = null
  
  def getByte(x:Int, y:Int) : Int = {
    var res=0;
    for(i <- 0 until 8 by 2){
      var Zr1=Crb(x+i)
      var Zi1=Cib(y)
      
      var Zr2=Crb(x+i+1)
      var Zi2=Cib(y)
      
      var b=0
      var j=49
      var goOn = true
      do{
        val nZr1=Zr1*Zr1-Zi1*Zi1+Crb(x+i)
        val nZi1=Zr1*Zi1+Zr1*Zi1+Cib(y)
        Zr1=nZr1;Zi1=nZi1
         
        val nZr2=Zr2*Zr2-Zi2*Zi2+Crb(x+i+1);
        val nZi2=Zr2*Zi2+Zr2*Zi2+Cib(y);
        Zr2=nZr2;Zi2=nZi2;
         
        if(Zr1*Zr1+Zi1*Zi1>4){b|=2;goOn = b != 3}
        if(Zr2*Zr2+Zi2*Zi2>4){b|=1;goOn = b!=3}
        j-=1
      }while(j>0&& goOn)
      res=(res<<2)+b;
    }
    return res^(-1);
  }

  def putLine(y:Int, line:Array[Byte]){
    for (xb <- 0 until line.length)
      line(xb)=getByte(xb*8,y).toByte
  }

  def main(args:Array[String]) {
    var N=6000
    if (args.length>=1) N = args(0).toInt
    
    Crb=new Array[Double](N+7); Cib=new Array[Double](N+7)
    val invN=2.0/N; for(i <- 0 until N){ Cib(i)=i*invN-1.0; Crb(i)=i*invN-1.5; }
    yCt=new AtomicInteger()
    out=Array.ofDim(N,(N+7)/8)
    
    val pool=new Array[Thread](2*Runtime.getRuntime().availableProcessors())
    for (i <- 0 until pool.length)
      pool(i)=new Thread(){
        override def run() {
          var y=0; while({y=yCt.getAndIncrement();y}<out.length) putLine(y,out(y))
        }
      }
    for (t <- pool) t.start()
    for (t <- pool) t.join()

    val stream = new BufferedOutputStream(System.out)
    stream.write(("P4\n"+N+" "+N+"\n").getBytes())
    for(i<- 0 until N) stream.write(out(i))
    stream.close()
   }
}
