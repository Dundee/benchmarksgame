/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

     contributed by Marc Millstone
*/


import scala.annotation.tailrec
import scala.actors.Futures._

object spectralnorm  {
def main(args: Array[String]) = {
   val n = (if (args.length>0) args(0).toInt else 100)
   val sTime = System.currentTimeMillis   
   val solution = RecursiveSpectralNorm(n).approximate 
   val totalTime = System.currentTimeMillis - sTime
   println("%.09f".format(solution)) 
 }
}


//Companion object for construction
object RecursiveSpectralNorm{
  def apply(size:Int) = {
   new RecursiveSpectralNorm(size)
  }
}


class RecursiveSpectralNorm(size:Int){
   //v = A'A*u
   //u = A'A*v

   //Evaluate matrix (and transpose) elements
   val A = (i: Int, j: Int) => 1.0/((i+j)*(i+j+1)/2 +i+1)
   val At = (j: Int, i: Int) => 1.0/((i+j)*(i+j+1)/2 +i+1)

   //For parallelization, get number of cores (virtual cores) 
   val numProcessors = Runtime.getRuntime.availableProcessors

   //Break up problem into Chunks correponding to rows
   val chunk = (size/numProcessors) 
 
   // generate a list of tuples of chunks to process
   // (0,chunk-1) (chunk, 2*chunk-1) ... (_, size-1)
   val chunkIndex = (0 to numProcessors-1).map(s=> s*chunk).zip( 
                ((1 to numProcessors-1).map(
                  s=>s*chunk-1).toList:::List((size-1))))

   // Mulitples M from row StartRow to endRow times a vector

   def subMatrixTimesVector(Mat: (Int,Int) => Double, 
                      indexTuple:Tuple2[Int,Int], vec:Array[Double]) = {
      val chunkSize = indexTuple._2 - indexTuple._1 + 1 
      var w = Array.fill(chunkSize)(0.)
      var i = indexTuple._1 
      var indexW = 0
      while (i <= indexTuple._2){
         var sum = 0.0
         var j = 0
         while(j < size){
            sum+= Mat(i,j)*vec(j)
            j+=1
         }
         w(indexW) = sum
         indexW+=1
         i+=1
      }
      w
     }



   // Uses non-blocking Scala futures to perform the required matrix-vector
   //products.  Uses a number of threads equal to the number of
   // available (virtual) cores.

   def fastATimesVector(vec:Array[Double]) = {
    var w:List[scala.actors.Future[Array[Double]]] = List()
    for(i <- 0 to (numProcessors -1))
      w = w ::: List(future{subMatrixTimesVector(A,chunkIndex(i),vec)})
    
    //Complete the future and concatenate the results
   w.map(s => s.apply).flatten.toArray
   }

   def fastATransposeTimesVector(vec:Array[Double]) = {
    var w:List[scala.actors.Future[Array[Double]]] = List()
    for(i <- 0 to (numProcessors -1))
      w = w ::: List(future{subMatrixTimesVector(At,chunkIndex(i),vec)})
    
    //Complete the future and concatenate the results
   w.map(s => s.apply).flatten.toArray
   }

   def matrixTimesVector(Mat: (Int,Int) => Double, vec:Array[Double]) = {
      var w = Array.fill(size)(0.)
      var i = 0
      subMatrixTimesVector(Mat,(0,size-1),vec)
   }

   def computeATransposeTimesATimesVec(u:Array[Double]) = {
      val w = fastATimesVector(u)
      fastATransposeTimesVector(w)
   }

   def updateUandV(u: Array[Double], v:Array[Double])
               : Tuple2[Array[Double], Array[Double]] = {
     val vnew = computeATransposeTimesATimesVec(u)
     val unew = computeATransposeTimesATimesVec(vnew)
     (unew,vnew)
   }

   def computeSolution(u:Array[Double], v:Array[Double]) = {
     var vbv,vv = 0.0
     var i = 0
     while (i < size) {
      vbv += u(i)*v(i)
      vv += v(i)*v(i)
      i += 1
     }
     math.sqrt(vbv/vv)
   }   
   

   //Uses a tail recursive helper function, approximateHelper to approximate
   //the spectral norm of A.  Observe that approximateHelper returns a tuple of
   //arrays.  The parallization is performed in the Matrix -vector product code
   // FastMatrixTimesVector
   def approximate() = { 
     // (u,v) = approximate(u,v) recursive call
     @tailrec def approximateHelper(iter: Int, 
                            UVpair:Tuple2[Array[Double],Array[Double]])
                            : Tuple2[Array[Double],Array[Double]] = {
      if (iter == 9)
        UVpair
      else { 
        approximateHelper(iter+1,updateUandV(UVpair._1,UVpair._2))
      }
     }
     val UV = approximateHelper(0, (Array.fill(size)(1.0),Array.fill(size)(1.0)))

     //Compute the result  
     computeSolution(UV._1, UV._2) 
   }
}
