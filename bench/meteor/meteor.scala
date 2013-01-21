/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Olof Kraigher
*/
import scala.collection.mutable.HashMap
import scala.collection.mutable.MutableList

object meteor {
  
  object Direction extends Enumeration {
    // Handle the hexagonal directions on the meteor board
    
    type Direction = Value
    val NW, W, SW, SE, E, NE = Value

    def rotate(dir : Direction, amount : Int) : Direction = 
      // Rotate a single direction a specific amount clockwise
      (dir.id + amount) % maxId
      
    def rotate(piece : Seq[Direction], amount : Int) : Seq[Direction] = 
      // Rotate a piece a specific amount clockwise
      piece.map(rotate(_, amount))
    
    def flip(dir : Direction) : Direction =
      // Flip a direction
      maxId - 1 - dir.id

    def flip(piece : Seq[Direction]) : Seq[Direction] =
      // Flip a piece
      piece.map(flip)
      
    def move(dir : Direction, pos : (Int, Int)) : Option[(Int, Int)] = {
      // Move a position in a specific direction
      val (x, y) = pos
      val oddy = y % 2
      val (nx, ny) = dir match {
        case E  => (x + 1, y)
        case W  => (x - 1, y)
        case NE => (x + oddy,     y - 1)
        case NW => (x + oddy - 1, y - 1)
        case SE => (x + oddy,     y + 1)
        case SW => (x + oddy - 1, y + 1)
      }
      
      if (0 <= nx && nx < 5 && 0 <= ny && ny < 10)
        Some((nx, ny))
      else
        None
    }
    
    // Convenient implicit conversion
    implicit def DirectionToInt(i : Int) : Direction = Direction(i)
  }

  
  object MaskTools {
    // Methods to manipulate bit masks
    
    import Direction._
    
    val even = (
             0x1fL 
          | (0x1fL << 10) 
          | (0x1fL << 20) 
          | (0x1fL << 30) 
          | (0x1fL << 40))
    
    val odd = even << 5
    val full = (1L << 50) - 1
    val west = 0x210842108421L
    val east = west << 4
          
    def firstZero(mask : Long, idx : Int = 0) : Int = {
      // Find the position of the first zero bit in mask
      var i = idx
      while (((mask >> i) & 1L) == 1L && i <= 63) {
        i += 1
      }
      return i
    } 
       
    def expand(mask : Long) : Long = {
      // Expand mask by growing it in every direction
      val evenMask = mask & even
      val oddMask = mask & odd
      val toE = (mask & ~east) << 1
      val toW = (mask & ~west) >> 1
      val toNW = (oddMask >> 5)  | ((evenMask & ~west) >> 6)
      val toNE = (evenMask >> 5) | ((oddMask & ~east) >> 4)
      val toSW = (oddMask << 5)  | ((evenMask & ~west) << 4)
      val toSE = (evenMask << 5) | ((oddMask & ~east) << 6)
      return (mask | toE | toW | toNW | toNE | toSW | toSE) & full
    }
    
    def floodFill(mask : Long, seed : Long) : Long = {
      /* Flood fill starting at the seed, the mask is
       * used to as the boundary 
       */
      var region = 0L
      var growth = seed
  
      do {
        region = growth
        growth = expand(region) & ~mask
      } while (growth != region)
        
      growth
    }
    
    def bitCount(mask : Long) : Int = {
      // Count the number of 1:s in mask
      var count = 0
      for (idx <- 0 until 50)
        if (((mask >> idx) & 1L) != 0L)
          count += 1
      return count
    }
    
    def noIslands(mask : Long) : Boolean = {
      // Check that the mask contains no islands
      // of a size not divisible by 5
      var m = mask
      var lastZero = -1
      
      while (m != full) {
        lastZero = firstZero(m, lastZero+1)
        val growth = floodFill(mask, 1L << lastZero)
        
        if (bitCount(growth) % 5 != 0) {
          return false
        }
        
        m |= growth
      }
      return true
    }
    
    def reverse(mask : Long) : Long = {
      // Bit reverse mask of 50 bits 
      var rev = 0L
      for (idx <- 0 until 50) {
        rev |= ((mask >> idx) & 1) << (49 - idx)
      }
      return rev
    }
  }
  
  object Pieces {
    /* Contains definition of all pieces
     * Converts pieces into masks
     */
    
    import MaskTools._
    import Direction._
    
    val pieces = Vector(
        List(E,  E,  E,  SE),
        List(SE, SW, W,  SW),
        List(W,  W,  SW, SE),
        List(E,  E,  SW, SE),
        List(NW, W,  NW, SE, SW),
        List(E,  E,  NE, W),
        List(NW, NE, NE, W),
        List(NE, SE, E,  NE),
        List(SE, SE, E,  SE),
        List(E,  NW, NW, NW))   
    
    def growToMask(
             piece : Seq[Direction], 
             pos   : (Int, Int), 
             accum : Long = 0L) 
                   : Option[Long] = {
      // Grow a piece into a bitmask from a starting position
      val (x,y) = pos;
      val bit = (1L << (5*y + x))
  
      if (piece.length == 0)
        Some(accum | bit)
      else
        move(piece.head, pos) match {
          case Some(newPos) => growToMask(piece.tail, newPos, accum | bit)
          case None => None
       }
    }
  
    
    def allMasksOfPiece(piece : Seq[Direction], rotations : Int) = {
      /* Create all rotated/flipped/transposed masks of a piece
       * returns vector ordered by the first 1-bit position.
       */
      val all = for (doflip <- List(false, true);
                     r <- 0 until rotations;
                     x <- 0 until 5;
                     y <- 0 until 10) yield {
        growToMask(rotate(if (doflip) flip(piece) else piece, r), (x,y))
      } 
      
      val some = all.collect {case Some(mask) if noIslands(mask) => mask}
      val byFirstOne = some.groupBy(mask => firstZero(~mask))
      Vector.tabulate(50)(idx => byFirstOne.getOrElse(idx, Vector()))
    }
    
    def allMasksOfColor(color : Int) = 
      /* Create all masks of color
       * The masks for color 5 is only rotated by half
       * producing only half the solutions where the other
       * half is found by reversing the first half
       */
      allMasksOfPiece(pieces(color), (if (color == 5) 3 else 6)) 
      
    val masksByColor = Vector.tabulate(pieces.size)(allMasksOfColor)
  }
  
  object Solver {
    // Solves the meteor puzzle
    import MaskTools.{firstZero, reverse}
    
    def solve() : Seq[String] = {
      // Solve the meteor puzzle
      
      // List of found solutions
      val solutions : MutableList[String] = MutableList()
      
      // Partial solution used during recursive search
      var partial : HashMap[Int, Long] = new HashMap()
      
      def solve(acc       : Long = 0L, 
                lastZero  : Int = 0, 
                remaining : Int = 0x3ff) {
        // Recursive solution search
       
        val idx = firstZero(acc, lastZero)
        
        if (remaining == 0) { 
          solutions += show(partial)
          solutions += show(partial.mapValues(reverse))
          return
        }
        
        var color = 0
        
        while (color < 10) {
          val cmask = 1 << color         
          if ((remaining & cmask) != 0) {
            for (mask  <- Pieces.masksByColor(color)(idx))
              if ((acc & mask) == 0L) {
                partial(color) = mask
                solve(acc | mask, idx + 1, remaining ^ cmask)
              }
          }
          color += 1
        }
          
      }
      solve()
      return solutions
    }
     
    def show(solution : collection.Map[Int, Long]) : String = {
      // Create a string representation of a solution
    
      val board : Array[Array[Char]] = Array.fill(10, 5)('-')
    
      for (color <- 0 to 9) { 
        
        val mask = solution.getOrElse(color, 0L)
        val colorChar = (color + '0').toChar
        
        for (x <- 0 until 5; y <- 0 until 10) {
          val pos = 1L << (5*y + x)
        
          if ((mask & pos) != 0) 
            board(y)(x) = colorChar
          
        }
      }
      
      // Indent odd rows
      def indent(y : Int) = 
        if (y % 2 == 1) " " else ""
      
      // Show a row
      def showRow(y : Int) = 
        board(y).foldLeft(indent(y))(_ + _ + " ")
  
      (0 until 10).foldLeft("")((acc, y) => acc + showRow(y) + "\n")
    }
 
  }
  
  def main(args: Array[String]): Unit = {
      val allSolutions = Solver.solve()
      val howMany = {
        if (args.length == 1)
          args.first.toInt
        else
          allSolutions.length
      }
      val solutions = allSolutions.take(howMany)
      println(solutions.length + " solutions found\n")
      println(solutions.min)
      println(solutions.max)
  }

}
