/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Adapted by Antti Lankila from the earlier Isaac Gouy's implementation
   Add multithread & tweaks from C++ by The Anh Tran
   Simplified bit logic and cleaned code by Robert F. Tobler
   Mono.Simd implementation by Nigel Delaney.
*/

using System;
using Mono.Simd;
using System.IO;
using System.Threading;

public class MandelBrot
{
   private static int n = 200;
   private static byte[][] data;
   private static int lineCount = -1;
   private static double[] xa;

   public static void Main (String[] args)
   {
      if (args.Length > 0)
         n = Int32.Parse (args [0]);
      Console.Out.WriteLine ("P4\n{0} {0}", n);
      int lineLen = (n - 1) / 8 + 1;
      data = new byte[n][];
      for (int i = 0; i < n; i++)
         data [i] = new byte[lineLen];
      xa = new double[n];
      for (int x = 0; x < n; x++)
         xa [x] = x * 2.0 / n - 1.5;
      var threads = new Thread[Environment.ProcessorCount];
      for (int i = 0; i < threads.Length; i++)
         (threads [i] = new Thread (MandelBrot.Calculate)).Start ();
      foreach (var t in threads)
         t.Join ();
      var s = Console.OpenStandardOutput ();
      for (int y = 0; y < n; y++)
         s.Write (data [y], 0, lineLen);
   }

   private static void Calculate ()
   {
      int y;
      double twoOverN = 2.0 / n;
      while ((y = Interlocked.Increment (ref lineCount)) < n) {
         var line = data [y];
         int xbyte = 0, bits = 1;
         double ci = y * twoOverN - 1.0;
         Vector2d Ci = new Vector2d (ci);
         Vector2d fours = new Vector2d (4.0, 4.0);
         for (int x = 0; x < xa.Length; x += 2) {
            Vector2d Cr = new Vector2d (xa [x], xa [x + 1]);//vector of constant real terms
            Vector2d Tempr = Cr * Cr;
            Vector2d Tempi = Ci * Ci;
            Vector2d Zr = Cr;
            Vector2d Zi = Ci;
            Vector2d Temp = Tempr - Tempi + Cr;
            if (bits > 0xff) {
               line [xbyte++] = (byte)(bits ^ -1);
               bits = 1;
            }
            int i = 49;
            int b = 0;
            do {
               Vector2d Zri = Zr * Zi;//calculate r*i for both
               Zi = Zri + Zri + Ci; //double that and add a constant 
               Zr = Temp;//pre-calculated on previous loop
               var V0 = Zr.InterleaveLow (Zi);//r0,i0
               var V1 = Zr.InterleaveHigh (Zi);//r1,i1
               V0 = V0 * V0;//r0^2,i0^2
               V1 = V1 * V1;
               var Length = V0.HorizontalAdd (V1);//(r0^2+i0^2),(r1^2+i1^2)
               Temp = V0.HorizontalSub (V1) + Cr;//(r0^2-i0^2),(r1^2-i1^2)
               //now to determine end condition, 
               var testVal = VectorOperations.ExtractByteMask ((Vector16sb)VectorOperations.CompareLessThan (fours, Length));
               if (testVal == 0) {
                  continue;
               } else {
                  if (testVal == 0xFFFF) {
                     b = 3;
                     break;
                  } else if (testVal == 0x00FF) {
                     b |= 2;
                     if (b == 3) {
                        break;
                     }
                  } else {
                     b |= 1;
                     if (b == 3) {
                        break;
                     }
                  }
               }
            } while (--i > 0);
            bits = (bits << 2) + b;
         }
         while (bits < 0x100)
            bits = (bits << 1);
         line [xbyte] = (byte)(bits ^ -1);
      }
   }
}
