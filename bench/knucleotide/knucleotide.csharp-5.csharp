/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/
 *
 * contributed by by Robert F. Tobler
 *  + byte processing, C# 3.0 idioms, frame level paralellism
 * modified by Jonathan C. Dickinson
 *  + unsafe code, string-free, micro-optimizations, best performance practices
 *   (33% faster on MSFT CLR)
 */

using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading;
using System.Diagnostics;

public sealed class ByteString : IEquatable<ByteString>, IComparable<ByteString>
{
   public readonly byte[] Array;
   public readonly int Start;
   public readonly int Length;
   private int HashCode;

   public unsafe ByteString(byte[] array, int start, int length)
   {
      Array = array; Start = start; Length = length;
      HashCode = 0;
      fixed (byte* ptrFixed = &Array[Start])
      {
         byte* ptr = ptrFixed;
         for (int i = 0; i < Length; i++)
            HashCode = HashCode * 31 + *ptr++;
      }
   }

   public unsafe ByteString(string text)
   {
      Start = 0; Length = text.Length;
      Array = Encoding.ASCII.GetBytes(text);
      HashCode = 0;
      fixed (byte* ptrFixed = &Array[Start])
      {
         byte* ptr = ptrFixed;
         for (int i = 0; i < Length; i++)
            HashCode = HashCode * 31 + *ptr++;
      }
   }

   public override int GetHashCode()
   {
      return HashCode;
   }

   public unsafe bool Equals(ByteString other)
   {
      if (Length != other.Length) return false;
      fixed (byte* thisFixed = &Array[Start], otherFixed = &other.Array[other.Start])
      {
         var tf = thisFixed;
         var of = otherFixed;
         for (int i = 0; i < Length; i++)
            if (*(tf++) != *(of++)) return false;
      }
      return true;
   }

   public unsafe int CompareTo(ByteString other)
   {
      // Converting to strings to compare is just wasteful.

      var len = Math.Min(this.Length, other.Length);
      fixed (byte* thisFixed = &Array[Start], otherFixed = &other.Array[other.Start])
      {
         var tf = thisFixed;
         var of = otherFixed;
         // We have to do this loop because of how strings compare.
         for (var i = 0; i < len; i++)
         {
            var c1 = (char)(*(tf++));
            var c2 = (char)(*(of++));
            var c = c1.CompareTo(c2);
            if (c != 0)
               return c;
         }
      }

      return this.Length.CompareTo(other.Length);
   }

   public override string ToString()
   {
      return Encoding.ASCII.GetString(Array, Start, Length);
   }
}

public static class Extensions
{
   public static readonly byte[] NewLine;

   static Extensions()
   {
      //NewLine = Environment.NewLine.Select(x => (byte)x).ToArray();
      NewLine = new[] { (byte)'\n' };
   }

   public static bool Contains(this byte[] buffer, byte[] data)
   {
      var index = 0;
      for (var i = 0; i < buffer.Length - data.Length; i++)
      {
         var b = buffer[i];

         // ToUpper
         if (b > 96 && b < 123)
            b = (byte)(b - 32);

         if (data[index] == b)
         {
            index++;
            if (index == data.Length)
            {
               return true;
            }
         }
         else
         {
            index = 0;
         }
      }
      return false;
   }

   public static IEnumerable<byte[]> ReadByteLines(this Stream stream)
   {
      // Read a stream as a set of byte arrays representing strings.
      var dynamic = new byte[85];
      var lineLength = 0;

      var buffer = new byte[4096]; // Ideally the system page size but I am not sure what Linux uses.
      var index = 0;
      var readCount = 0;

      while ((readCount = stream.Read(buffer, 0, buffer.Length)) != 0)
      {
         for (var i = 0; i < readCount; i++)
         {
            var b = buffer[i];

            // ToUpper
            if (b > 96 && b < 123)
               b = (byte)(b - 32);

            // Check for a newline.
            if (NewLine[index] == b)
            {
               index++;
               if (index == NewLine.Length)
               {
                  if (lineLength == 0)
                     yield return new byte[0];
                  var result = new byte[lineLength];
                  Buffer.BlockCopy(dynamic, 0, result, 0, lineLength);
                  yield return result;
                  lineLength = 0;
                  index = 0;
               }
            }
            else
            {
               // Otherwise append what we had of the newline.
               for (var j = 0; j < index; j++)
               {
                  Append(ref dynamic, ref lineLength, NewLine[j]);
               }

               // And the data.
               index = 0;
               Append(ref dynamic, ref lineLength, b);
            }
         }
      }
   }

   private static void Append(ref byte[] dynamic, ref int lineLength, byte b)
   {
      var newLength = lineLength + 1;
      if (dynamic.Length < newLength)
      {
         // Allocate into an new array but use a DMA function
         // instead of a silly O(n) Array.Copy/Resize.
         var newArr = new byte[dynamic.Length + 85];
         Buffer.BlockCopy(dynamic, 0, newArr, 0, dynamic.Length);
         dynamic = newArr;
      }
      dynamic[lineLength] = b;
      lineLength = newLength;
   }

   public static byte[] Concat(this IList<byte[]> bytes, int totalCount)
   {
      // Once again we use DMA.
      var buffer = new byte[totalCount];
      var pos = 0;
      for (var i = 0; i < bytes.Count; i++)
      {
         var arr = bytes[i];
         Buffer.BlockCopy(arr, 0, buffer, pos, arr.Length);
         pos += arr.Length;
      }
      return buffer;
   }
}

public static class Program
{
   public static int TaskCount;
   public static int Current = -1;
   public static KNucleotide[] kna;

   public static void Main(string[] args)
   {
      // The help page asks to deal with command-line args.
      var source = Console.OpenStandardInput();
      var input = new List<byte[]>();
      var totalCount = 0;

      var hasSeenThree = false;
      foreach (var line in source.ReadByteLines())
      {
         if (!hasSeenThree)
         {
            hasSeenThree = line.Contains(new[] { (byte)'>', (byte)'T', (byte)'H', (byte)'R', (byte)'E', (byte)'E' });
            continue;
         }
         if (line[0] == (byte)'>') break;
         if (line[0] != (byte)';')
         {
            totalCount += line.Length;
            input.Add(line);
         }
      }

      var lengths = new[] { 1, 2, 3, 4, 6, 12, 18 };
      TaskCount = lengths.Aggregate(0, (cnt, len) => cnt + len); // Wasteful but likely not an issue.
      kna = new KNucleotide[TaskCount];

      var bytes = input.Concat(totalCount);
      lengths.Aggregate(0, (cnt, len) =>
      {
         for (int i = 0; i < len; i++)
            kna[cnt + i] = new KNucleotide(bytes, len, i);
         return cnt + len;
      });

      // This technically should be faster, but my profiler is broken.
      var events = new AutoResetEvent[Environment.ProcessorCount];
      for (int i = 0; i < events.Length; i++)
      {
         events[i] = new AutoResetEvent(false);
         new Thread(CountFrequencies).Start(events[i]);
      }

      WaitHandle.WaitAll(events);

      // Converting these to byte arrays at compile time is slightly wasteful but likely premature.
      var seqs = new[] { null, null,
            "GGT", "GGTA", "GGTATT", "GGTATTTTAATT",
            "GGTATTTTAATTTATAGT"};

      int index = 0;
      lengths.Aggregate(0, (cnt, len) =>
      {
         if (len < 3)
         {
            for (int i = 1; i < len; i++)
               kna[cnt].AddFrequencies(kna[cnt + i]);
            kna[cnt].WriteFrequencies();
         }
         else
         {
            var fragment = seqs[index];
            int freq = 0;
            for (int i = 0; i < len; i++)
               freq += kna[cnt + i].GetCount(fragment);
            Console.WriteLine("{0}\t{1}", freq, fragment);
         }
         index++;
         return cnt + len;
      });
   }

   static void CountFrequencies(object state)
   {
      int index;
      while ((index = Interlocked.Increment(ref Current)) < TaskCount)
         kna[index].KFrequency();
      ((AutoResetEvent)state).Set();
   }

}

public sealed class KNucleotide
{

   private sealed class Count
   {
      public int V;
      public Count(int v) { V = v; }
   }

   private Dictionary<ByteString, Count> frequencies
      = new Dictionary<ByteString, Count>();

   private byte[] sequence;
   int length;
   int frame;

   public KNucleotide(byte[] s, int l, int f)
   {
      sequence = s; length = l; frame = f;
   }

   public void AddFrequencies(KNucleotide other)
   {
      foreach (var kvp in other.frequencies)
      {
         Count count;
         if (frequencies.TryGetValue(kvp.Key, out count))
            count.V += kvp.Value.V;
         else
            frequencies[kvp.Key] = kvp.Value;
      }
   }

   public void WriteFrequencies()
   {
      var items = frequencies.ToArray();
      Array.Sort(items, SortByFrequencyAndCode); // Apparantly faster, who knows?
      double percent = 100.0 / (sequence.Length - length + 1);
      foreach (var item in items)
         Console.WriteLine("{0} {1:f3}",
                  item.Key.ToString(), item.Value.V * percent);
      Console.WriteLine();
   }

   public int GetCount(string fragment)
   {
      Count count;
      if (!frequencies.TryGetValue(new ByteString(fragment), out count))
         count = new Count(0);
      return count.V;
   }

   public void KFrequency()
   {
      int n = sequence.Length - length + 1;
      for (int i = frame; i < n; i += length)
      {
         var key = new ByteString(sequence, i, length);
         Count count;
         if (frequencies.TryGetValue(key, out count))
            count.V++;
         else
            frequencies[key] = new Count(1);
      }
   }

   int SortByFrequencyAndCode(
         KeyValuePair<ByteString, Count> i0,
         KeyValuePair<ByteString, Count> i1)
   {
      int order = i1.Value.V.CompareTo(i0.Value.V);
      if (order != 0) return order;
      return i0.Key.CompareTo(i1.Key);
   }
}
