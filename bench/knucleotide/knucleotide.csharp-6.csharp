/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Eamon Nerbonne
*/
using System;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Threading.Tasks;

static class Program {
  const string bases = "ACGT";
  static byte?[] toBase = new byte?['t' + 1];

  public static void Main() {
    for (var i = 0; i < 4; i++)
      toBase["acgt"[i]] = (byte)i;

    //Start concurrent workers that will count dna fragments
    var workers = new[] { 1, 2, 3, 4, 6, 12, 18 }.Select(len => {
      var queue = new BlockingCollection<ulong[]>(2);
      return new {
        len,
        queue,
        task = Task.Factory.StartNew(
          () =>
            //use a sparse hash (dictionary) for longer fragments
            len > 8 ? new Sparse(queue, len)
            //...and use a dense hash (aka array) for very short fragments.
            : (ICounter)new Dense(queue, len),
          TaskCreationOptions.LongRunning)
      };
    }).ToArray();

    //Read lines into chunks.  The exact size isn't that important.
    //Smaller chunks are more concurrent but less CPU efficient.
    foreach (var chunk in LinesToChunks(1 << 18))
      //Pass chunks into concurrent consumers; add to last workers first
      //as a minor threading optimization.
      foreach (var w in workers.Reverse())
        w.queue.Add(chunk);

    foreach (var w in workers.Reverse())
      w.queue.CompleteAdding();

    //Show output for each consumer
    foreach (var w in workers) {
      if (w.len < 3)
        Console.WriteLine(((Dense)w.task.Result).Summary(w.len));
      else {
        var dna = "ggtattttaatttatagt".Substring(0, w.len);
        Console.WriteLine(
          w.task.Result.Count(dna.Aggregate(0ul,
              (v, c) => v << 2 | toBase[c].Value))
          + "\t" + dna.ToUpper()
        );
      }
    }
  }

  static IEnumerable<ulong[]> LinesToChunks(int size) {
    string line;
    while ((line = Console.ReadLine()) != null)
      if (line.StartsWith(">THREE"))
        break;

    //we just skipped all lines upto section three

    int i = 0;
    var arr = new ulong[size];
    ulong dna = 0;

    while ((line = Console.ReadLine()) != null)
      foreach (var c in line) {
        dna = dna << 2 | toBase[c].Value;
        arr[i++] = dna;
        if (i == size) {
          //ok, our batch is full, so yield it to consumers.
          yield return arr;
          i = 0;
          arr = new ulong[size];
        }
      }

    if (i > 0) {
      //last batch isn't entirely full, but don't forget it.
      Array.Resize(ref arr, i);
      yield return arr;
    }
  }

  struct Dense : ICounter {
    public Dense(BlockingCollection<ulong[]> seq, int len) {
      counts = new int[1 << len * 2];
      int mask = (1 << len * 2) - 1;
      int i = 0;

      foreach (var arr in seq.GetConsumingEnumerable())
        foreach (var dna in arr) {
          i++;
          if (i >= len) //only count dna if its already long enough
            counts[(int)dna & mask]++;
        }
    }
    int[] counts;
    public int Count(ulong dna) { return counts[(int)dna]; }
    public string Summary(int len) {
      var scale = 100.0 / counts.Sum();
      return string.Concat(
        counts.Select((c, dna) => new {
          p = c * scale,
          dna = string.Concat(Enumerable.Range(0, len).Reverse()
                  .Select(i => bases[dna >> i * 2 & 3]))
        })
          .OrderByDescending(x => x.p).ThenBy(x => x.dna)
          .Select(x => x.dna + " " + x.p.ToString("f3") + "\n")
        );
    }
  }

  struct Sparse : ICounter {
    public Sparse(BlockingCollection<ulong[]> seq, int len) {
      var mask = (1ul << len * 2) - 1;
      var first = seq.GetConsumingEnumerable().First();
      
      counts = Enumerable.Range(0, Environment.ProcessorCount+1>>1).Select(p =>
        Task.Factory.StartNew(() => {
          var d = new Dictionary<ulong, IntRef>();
          if (p == 0)
            foreach (var dna in first.Skip(len - 1))
              //only count dna if its already long enough
              Add(d, dna & mask, 1);

          foreach (var arr in seq.GetConsumingEnumerable())
            foreach (var dna in arr)
              //only count dna if its already long enough
              Add(d, dna & mask, 1);
          return d;
        })
      ).ToArray().Select(t => t.Result).Aggregate((a, b) => {
        foreach (var kv in b)
          Add(a, kv.Key, kv.Value.val);
        return a;
      });
    }
    Dictionary<ulong, IntRef> counts;
    static void Add(Dictionary<ulong, IntRef> dict, ulong dna, int x) {
      IntRef count;
      if (!dict.TryGetValue(dna, out count))
        dict[dna] = new IntRef { val = x };
      else
        count.val += x;
    }

    public int Count(ulong dna) {
      IntRef count;
      return counts.TryGetValue(dna, out count) ? count.val : 0;
    }
  }
  class IntRef { public int val; }
  interface ICounter {
    //void Add(ulong dna);
    int Count(ulong dna);
  }
}