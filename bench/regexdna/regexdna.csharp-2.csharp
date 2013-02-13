/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/
 *
 * contributed by Jimmy Tang
 * modified by Sindhudweep Narayan Sarkar
*/

using System;
using System.Linq;
using System.Threading;
using System.Collections.Generic;
using System.Text.RegularExpressions;
using System.Threading.Tasks;

class regexdna {
   static void Main() {
      string sequence = Console.In.ReadToEnd();
      int initialLength = sequence.Length;
            
      sequence = Regex.Replace(sequence, ">.*\n|\n", "");
      int codeLength = sequence.Length;
      
      string[] variants = {
      "agggtaaa|tttaccct"
      ,"[cgt]gggtaaa|tttaccc[acg]"
      ,"a[act]ggtaaa|tttacc[agt]t"
      ,"ag[act]gtaaa|tttac[agt]ct"
      ,"agg[act]taaa|ttta[agt]cct"
      ,"aggg[acg]aaa|ttt[cgt]ccct"
      ,"agggt[cgt]aa|tt[acg]accct"
      ,"agggta[cgt]a|t[acg]taccct"
      ,"agggtaa[cgt]|[acg]ttaccct"
      };
      
      Parallel.For(0, variants.Length, i => 
         { 
            var pattern = new Regex(variants[i], RegexOptions.Compiled); 
            variants[i] += " " + pattern.Matches(sequence).Count; 
         });
      Console.WriteLine(string.Join("\n", variants));
      
      var dict = new Dictionary<string, string> {
         {"B", "(c|g|t)"}, {"D", "(a|g|t)"},   {"H", "(a|c|t)"}, {"K", "(g|t)"},
         {"M", "(a|c)"},   {"N", "(a|c|g|t)"}, {"R", "(a|g)"},   {"S", "(c|g)"},
         {"V", "(a|c|g)"}, {"W", "(a|t)"},     {"Y", "(c|t)"} 
      };
      sequence = new Regex("[WYKMSRBDVHN]", RegexOptions.Compiled).Replace(sequence, m => dict[m.Value]);
      Console.WriteLine("\n{0}\n{1}\n{2}", initialLength, codeLength, sequence.Length);
   }
}
