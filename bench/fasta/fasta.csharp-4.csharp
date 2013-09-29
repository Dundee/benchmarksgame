/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Serge Smith
*/

using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

class Fasta
{
  const int LineLength = 60;

  const int IM = 139968;
  const int IA = 3877;
  const int IC = 29573;
  static int seed = 42;

  public static void Main(string[] args)
  {
    int n = args.Length > 0 ? Int32.Parse(args[0]) : 1000;

    MakeCumulative(IUB);
    MakeCumulative(HomoSapiens);

    using (var s = Console.OpenStandardOutput())
    {
      MakeRepeatFasta("ONE", "Homo sapiens alu", Encoding.ASCII.GetBytes(ALU), n * 2, s);
      MakeRandomFasta("TWO", "IUB ambiguity codes", IUB, n * 3, s);
      MakeRandomFasta("THREE", "Homo sapiens frequency", HomoSapiens, n * 5, s);
    }

  }
  static void MakeRandomFasta(string id, string desc, Frequency[] a, int n, Stream s)
  {
    byte[] descStr = Encoding.ASCII.GetBytes(">" + id + " " + desc + "\n");
    s.Write(descStr, 0, descStr.Length);

    var res = RandomSequence(n, LineLength * 40)        
      .AsParallel()
      .AsOrdered()
      .Select(rnd =>
        {
          var resLength = (rnd.Length / LineLength) * (LineLength + 1);
          if (rnd.Length % LineLength != 0)
          {
            resLength += rnd.Length % LineLength + 1;
          }

          var buf = new byte[resLength];
          var index = 0;
          for (var i = 0; i < rnd.Length; i += LineLength)
          {
            var len = Math.Min(LineLength, rnd.Length - i);
            for (var j = 0; j < len; ++j)
              buf[index++] = SelectRandom(a, rnd[i + j]);
            buf[index++] = (byte)'\n';
          }
          return buf;
        })
      .AsSequential();
    ;
    foreach (var r in res)
      s.Write(r, 0, r.Length);

  }
  static void MakeRepeatFasta(string id, string desc, byte[] alu, int n, Stream s)
  {
    var index = 0;
    int m = 0;
    int k = 0;
    int kn = alu.Length;
    var buf = new byte[1024];

    byte[] descStr = Encoding.ASCII.GetBytes(">" + id + " " + desc + "\n");
    s.Write(descStr, 0, descStr.Length);

    while (n > 0)
    {
      m = n < LineLength ? n : LineLength;

      if (buf.Length - index < m)
      {
        s.Write(buf, 0, index);
        index = 0;
      }

      for (int i = 0; i < m; i++)
      {
        if (k == kn)
          k = 0;

        buf[index++] = alu[k];
        k++;
      }

      buf[index++] = (byte)'\n';
      n -= LineLength;
    }

    if (index != 0)
      s.Write(buf, 0, index);
  }
  static byte SelectRandom(Frequency[] a, int _r)
  {
    //double r = random();
    double r = _r * 1.0 / IM;

    for (int i = 0; i < a.Length; i++)
      if (r < a[i].p)
        return a[i].c;

    return a[a.Length - 1].c;
  }

  static void MakeCumulative(Frequency[] a)
  {
    double cp = 0.0;
    for (int i = 0; i < a.Length; i++)
    {
      cp += a[i].p;
      a[i].p = cp;
    }
  }

  static string ALU =
    "GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG" +
    "GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA" +
    "CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT" +
    "ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA" +
    "GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG" +
    "AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC" +
    "AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA";

  class Frequency
  {
    public readonly byte c;
    public double p;

    public Frequency(char c, double p)
    {
      this.c = (byte)c;
      this.p = p;
    }
  }

  static Frequency[] IUB = {
	new Frequency ('a', 0.27)
		,new Frequency ('c', 0.12)
		,new Frequency ('g', 0.12)
		,new Frequency ('t', 0.27)

		,new Frequency ('B', 0.02)
		,new Frequency ('D', 0.02)
		,new Frequency ('H', 0.02)
		,new Frequency ('K', 0.02)
		,new Frequency ('M', 0.02)
		,new Frequency ('N', 0.02)
		,new Frequency ('R', 0.02)
		,new Frequency ('S', 0.02)
		,new Frequency ('V', 0.02)
		,new Frequency ('W', 0.02)
		,new Frequency ('Y', 0.02)
};

  static Frequency[] HomoSapiens = {
	new Frequency ('a', 0.3029549426680)
		,new Frequency ('c', 0.1979883004921)
		,new Frequency ('g', 0.1975473066391)
		,new Frequency ('t', 0.3015094502008)
};


  static int[] random(int count)
  {
    int[] result = new int[count];
    for (var i = 0; i < result.Length; ++i)
    {
      seed = (seed * IA + IC) % IM;
      result[i] = seed;
    }
    return result;
  }
  static IEnumerable<int[]> RandomSequence(int count, int len = 1024)
  {
    for (var i = 0; i < count; i += len)
    {
      var rnd = random(Math.Min(len, count - i));
      yield return rnd;
    }
  }

}
