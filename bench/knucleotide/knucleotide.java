/* The Computer Language Benchmarks Game
 http://benchmarksgame.alioth.debian.org/

 contributed by Rikard Mustajärvi
*/


import java.io.FilterInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.lang.management.ManagementFactory;
import java.lang.reflect.Field;
import java.nio.ByteBuffer;
import java.nio.channels.Channels;
import java.nio.channels.ReadableByteChannel;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.TreeMap;
import java.util.concurrent.ArrayBlockingQueue;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.LinkedBlockingQueue;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.atomic.AtomicLong;


public class knucleotide {
   static final int LINE_WIDTH = 60;
   static final int BITS_PER_CHAR = 2;
   static final int BITS_MASK = Byte.parseByte("11", 2);

   static final byte A = Byte.parseByte("11", 2);
   static final byte C = Byte.parseByte("01", 2);
   static final byte G = Byte.parseByte("00", 2);
   static final byte T = Byte.parseByte("10", 2);

   private static final byte[] ENCODE;
   private static final byte[] DECODE;

   static {
      ENCODE = new byte['t' + 1];
      ENCODE['a'] = A;
      ENCODE['A'] = A;
      ENCODE['c'] = C;
      ENCODE['C'] = C;
      ENCODE['g'] = G;
      ENCODE['G'] = G;
      ENCODE['t'] = T;
      ENCODE['T'] = T;

      DECODE = new byte[4];
      DECODE[A] = 'A';
      DECODE[C] = 'C';
      DECODE[G] = 'G';
      DECODE[T] = 'T';
   }

   // try to determine effective number of cores
   private static final int concurrency =
         AffinityDetectionHelper.isLockedToSingleCore() ?
               1 : Runtime.getRuntime().availableProcessors();

   public static void main(String[] args) throws Exception {
      InputStream is;
      is = System.in;
//      is = new FileInputStream("knucleotide-input_n25m.txt");
      final IsReader isr = new IsReader(is);

      findGenome(isr);

      int[] ks = new int[]{1, 2, 3, 4, 6, 12, 18};

      LinkedBlockingQueue<AbstractCounter> counterQueue = new LinkedBlockingQueue<AbstractCounter>();

      Counters counters = new Counters(ks);
      for (AbstractCounter counter : counters.getCounters()) {
         counterQueue.put(counter);
      }

      List<LinkedBlockingQueue<WorkChunk>> workQueues = new ArrayList<LinkedBlockingQueue<WorkChunk>>();

      final ExecutorService executor = Executors.newFixedThreadPool(concurrency);
      for (int i = 0; i < concurrency; i++) {
         LinkedBlockingQueue<WorkChunk> workQueue = new LinkedBlockingQueue<WorkChunk>();
         workQueues.add(workQueue);
         executor.submit(new CounterProcesser(counterQueue, workQueue));
      }

      int chunkSize = LINE_WIDTH*100;
      while (!isr.eof()) {
         byte[] chunk = isr.getNextChunk(chunkSize);
         int len = encode(chunk);

         WorkChunk wc = new WorkChunk();
         wc.data = chunk;
         wc.length = len;

         for (LinkedBlockingQueue<WorkChunk> wcQueue : workQueues) {
            wcQueue.add(wc);
         }

         chunkSize = LINE_WIDTH*5000;
      }

      for (LinkedBlockingQueue<WorkChunk> wcQueue : workQueues) {
         wcQueue.add(WorkChunk.END_MESSAGE);
      }

      executor.shutdown();
      executor.awaitTermination(1, TimeUnit.MINUTES);

      writeRelativeFreqs(counters.getCounter(1));
      System.out.println();

      writeRelativeFreqs(counters.getCounter(2));
      System.out.println();

      String[] nucleotides = {"GGT", "GGTA", "GGTATT", "GGTATTTTAATT", "GGTATTTTAATTTATAGT"};
      for (String nucleotide : nucleotides) {
         System.out.println(String.format("%d\t%s", counters.getCount(nucleotide), nucleotide));
      }
   }

   static class WorkChunk {
      public static final WorkChunk END_MESSAGE = new WorkChunk();

      public byte[] data;
      public int length;
   }

   static class CounterProcesser implements Runnable {
      private final LinkedBlockingQueue<AbstractCounter> counterQueue;
      private final LinkedBlockingQueue<WorkChunk> workQueue;

      public CounterProcesser(LinkedBlockingQueue<AbstractCounter> counterQueue, LinkedBlockingQueue<WorkChunk> workQueue) {
         this.counterQueue = counterQueue;
         this.workQueue = workQueue;
      }

      @Override
      public void run() {
         try {
            AbstractCounter counter;

            List<WorkChunk> workList = new ArrayList<WorkChunk>(1024);

            // process first counter by taking from work queue
            counter = counterQueue.poll();

            WorkChunk wc = workQueue.take();
            counter.seed(wc.data, wc.length);

            workList.add(wc);

            while ( (wc = workQueue.take()) != WorkChunk.END_MESSAGE) {
               workList.add(wc);
               counter.consume(wc.data, 0, wc.length);
            }

            workList.add(WorkChunk.END_MESSAGE);

            // subsequential counters will take work from list
            while ( (counter = counterQueue.poll()) != null) {
               Iterator<WorkChunk> wcIt = workList.iterator();

               wc = wcIt.next();
               counter.seed(wc.data, wc.length);

               while ( (wc = wcIt.next()) != WorkChunk.END_MESSAGE) {
                  counter.consume(wc.data, 0, wc.length);
               }
            }
         } catch (Exception e) {
            throw new RuntimeException(e);
         }
      }
   }

   static int encode(byte[] chunk) {
      for (int i = 0 ; i < chunk.length; i++) {
         chunk[i] = ENCODE[chunk[i]];
      }
      return chunk.length;
   }

   static int seedCounters(byte[] chunk, AbstractCounter[] counters) throws IOException {
      int len = encode(chunk);
      for (AbstractCounter c : counters) {
         c.seed(chunk, len);
      }

      return len;
   }

   static void findGenome(IsReader isr) throws IOException {
      byte[] line = new byte[LINE_WIDTH];

      while (true) {
         isr.readLine(line, 0);
         if (line[0] == '>' && line[1] == 'T' && line[2] == 'H' && line[3] == 'R') {
            break;
         }
      }
   }

   static void writeRelativeFreqs(AbstractCounter counter) {
      if (counter == null) return; // simplify debugging

      class CountString {
         Integer count;
         String string;
      }

      List<CountString> list = new ArrayList<CountString>();

      int sum = 0;
      int to = 1<<(counter.shift);
      for (int key = 0; key < to; key++) {
         int count = counter.getCount(key);
         sum += count;

         StringBuilder sb = new StringBuilder();
         int tmp = key;
         for (int j = 0; j < counter.k; j++) {
            byte b = (byte)(tmp & BITS_MASK);
            tmp >>= BITS_PER_CHAR;
            sb.append((char)DECODE[b]);
         }
         sb.reverse();

         CountString cs = new CountString();
         cs.count = count;
         cs.string = sb.toString();
         list.add(cs);
      }

      Collections.sort(list, new Comparator<CountString>() {
         @Override
         public int compare(CountString o1, CountString o2) {
            int res = -o1.count.compareTo(o2.count);
            if (res == 0) res = o1.string.compareTo(o2.string);
            return res;
         }
      });

      for (CountString cs : list) {
         System.out.println(String.format("%s %.3f", cs.string, (100.0f*cs.count)/sum));
      }
   }

   static int toIntKey(String str) {
      int k = 0;
      for (byte b : str.getBytes()) {
         k = k << BITS_PER_CHAR | ENCODE[b];
      }
      return k;
   }

   static long toLongKey(String str) {
      long k = 0;
      for (byte b : str.getBytes()) {
         k = k << BITS_PER_CHAR | ENCODE[b];
      }
      return k;
   }

   static class Counters {
      private final static int SMALL_BIG_THRESHOLD = 9; // 2^(9*2)*4 = 2mb, lets make it possible to keep the tables in L3 cache

      private final Map<Integer, Map<Byte, AbstractCounter>> counterMap = new TreeMap<Integer, Map<Byte, AbstractCounter>>();
      private final AbstractCounter[] counters;

      public Counters(int[] ks) {
         byte[] letters = new byte[]{A, C, G, T};

         Set<AbstractCounter> counterSet = new HashSet<AbstractCounter>();
         for (int k : ks) {
            Map<Byte, AbstractCounter> lastChar2Counter = new TreeMap<Byte, AbstractCounter>();
            counterMap.put(k, lastChar2Counter);

            if (k <= SMALL_BIG_THRESHOLD) {
               SmallCounter counter = new SmallCounter(k);
               for (byte letter : letters) {
                  lastChar2Counter.put(letter, counter);
               }
            } else {
               if (concurrency > 1) {
                  // one counter per letter
                  for (byte letter : letters) {
                     BigCounter counter = new BigCounter(k, letter);
                     lastChar2Counter.put(letter, counter);
                  }
               } else {
                  // same counter for all letters
                  BigCounter counter = new BigCounter(k, (byte)-1);
                  for (byte letter : letters) {
                     lastChar2Counter.put(letter, counter);
                  }
               }

            }

            counterSet.addAll(lastChar2Counter.values());
         }

         counters = counterSet.toArray(new AbstractCounter[0]);
         Arrays.sort(counters, new Comparator<AbstractCounter>() {
            @Override
            public int compare(AbstractCounter o1, AbstractCounter o2) {
               if (o1 instanceof BigCounter && o2 instanceof BigCounter) {
                  return ((BigCounter)o2).letter - ((BigCounter)o1).letter;
               } else {
                  return o2.k - o1.k;
               }

            }
         });
      }

      int getCount(String nucleotide) {
         int length = nucleotide.length();
         byte lastChar = ENCODE[nucleotide.charAt(length - 1)];

         int count = 0;

         try {
            count = counterMap.get(length).get(lastChar).getCount(nucleotide);
         } catch (NullPointerException e) {} // beautiful code...

         return count;
      }

      AbstractCounter getCounter(int k) {
         if (k > SMALL_BIG_THRESHOLD) throw new UnsupportedOperationException();

         AbstractCounter counter = null;
         try {
            counter = counterMap.get(k).get(A); // for small counters all letters map to the same counter
         } catch (NullPointerException e) {}

         return counter;
      }

      AbstractCounter[] getCounters() {return  counters;}
   }
}


final class IsReader {
   static final int IO_BUFFER_SIZE = 256*1024;

   final ReadableByteChannel rbc;
   final ByteBuffer bb;

   int index; // current index into backing array
   int length = 0; // the number of bytes in the backing array
   byte[] backingArray;
   boolean eof = false;

   public IsReader(InputStream is) throws Exception {
      rbc = extractChannel(is);
      bb = ByteBuffer.allocateDirect(IO_BUFFER_SIZE);

      backingArray = new byte[IO_BUFFER_SIZE];
      index = 0;

      readMoreIfNeeded();
   }

   private void readMoreIfNeeded() throws IOException {
      while (length == index) {
         length = rbc.read(bb);
         if (length == -1) {
            length = 0;
            eof = true;
            break;
         }
         bb.flip();
         bb.get(backingArray, 0, length);
         index = 0;
      }
   }

   public byte get() throws IOException {
      readMoreIfNeeded();
      return backingArray[index++];
   }

   public int readLine(byte[] dst, int dstOffset) throws IOException {
      int dstIndex = dstOffset;
      int dstLength = dst.length;

      outer:
      while (!eof()) {
         readMoreIfNeeded();

         int index = this.index;
         int length = this.length;

         while (index < length) {
            if (dstIndex >= dstLength) {
               this.index = index;
               break outer;
            }

            byte b = backingArray[index++];

            if (b == '\n') {
               this.index = index;
               break outer;
            }

            dst[dstIndex++] = b;
         }

         this.index = index;
      }

      return dstIndex - dstOffset;
   }

   public byte[] getNextChunk(int size) throws IOException {
      size = (size <= 0 ? IO_BUFFER_SIZE : size);
      byte[] tmp = new byte[size];
      int offset = 0;

      while (true) {
         if (eof()) {
            tmp = Arrays.copyOf(tmp, offset);
            break;
         }

         int count = readLine(tmp, offset);
         offset += count;

         if (offset == tmp.length) break;
      }

      return tmp;
   }

   public boolean eof() {
      return eof;
   }

   private static ReadableByteChannel extractChannel(InputStream in)
         throws NoSuchFieldException, IllegalAccessException {

      Field f = FilterInputStream.class.getDeclaredField("in");
      f.setAccessible(true);

      while (in instanceof FilterInputStream) {
         in = (InputStream) f.get(in);
      }

      return Channels.newChannel(in);
   }
}

abstract class AbstractCounter {
   protected final int k;

   protected final int shift;

   protected AbstractCounter(final int k) {
      this.k = k;
      shift = k*knucleotide.BITS_PER_CHAR;
   }

   public abstract void seed(byte[] mapped, int length);
   public abstract void consume(byte[] mapped, int offset, int length);
   public abstract int getCount(int key);
   public abstract int getCount(String key);
}

final class SmallCounter extends AbstractCounter {
   protected final int mask;

   int readingFrame = 0;

   final int[] counts;

   public SmallCounter(int k) {
      super(k);

      int mask = 1;
      int n = shift;
      while (--n > 0) {
         mask = mask << 1 | 1;
      }
      this.mask = mask;

      counts = new int[(int)Math.round(Math.pow(2, k*knucleotide.BITS_PER_CHAR))];
   }

   @Override
   public void seed(byte[] mapped, int length) {
      if (length < k) {
         throw new IllegalArgumentException("length must be >= " + k);
      }
      for (int i = 0; i < k; i++) {
         readingFrame = (readingFrame << knucleotide.BITS_PER_CHAR) | mapped[i];
      }
      counts[readingFrame]++;
      consume(mapped, k, length);
   }

   @Override
   public void consume(byte[] mapped, int offset, int length) {
      int readingFrame = this.readingFrame;
      int mask = this.mask;
      int[] counts = this.counts;
      for (int i = offset; i < length ; i++) {
         readingFrame = (readingFrame << knucleotide.BITS_PER_CHAR) | mapped[i];
         readingFrame = readingFrame & mask;
         counts[readingFrame]++;
      }
      this.readingFrame = readingFrame;
   }

   @Override
   public int getCount(int key) {
      return counts[key];
   }

   @Override
   public int getCount(String key) {
      return getCount(knucleotide.toIntKey(key));
   }
}

final class BigCounter extends AbstractCounter {
   protected final long mask;

   long readingFrame = 0;

   private final PackedHash h = new PackedHash();

   final byte letter;

   public BigCounter(int k, byte letter) {
      super(k);

      this.letter = letter;

      long mask = 1;
      int n = shift;
      while (--n > 0) {
         mask = mask << 1 | 1;
      }
      this.mask = mask;
   }

   @Override
   public void seed(byte[] mapped, int length) {
      if (length < k) {
         throw new IllegalArgumentException("length must be >= " + k);
      }

      for (int i = 0; i < k - 1; i++) {
         readingFrame = (readingFrame << knucleotide.BITS_PER_CHAR) | mapped[i];
      }

      consume(mapped, k - 1, length);
   }


   @Override
   public void consume(byte[] mapped, int offset, int length) {
      if (letter == -1) {
         // count all letters
         countAll(mapped, offset, length);
      } else {
         // count specific letter
         countSpecific(mapped, offset, length);
      }
   }

   public void countAll(byte[] mapped, int offset, int length) {
      long readingFrame = this.readingFrame;
      long mask = this.mask;

      for (int i = offset; i < length ; i++) {
         byte curLetter = mapped[i];
         readingFrame = (readingFrame << knucleotide.BITS_PER_CHAR) | curLetter;
         readingFrame = readingFrame & mask;
         countCurrent(readingFrame);
      }

      this.readingFrame = readingFrame;
   }

   public void countSpecific(byte[] mapped, int offset, int length) {
      byte letter = this.letter;
      long readingFrame = this.readingFrame;
      long mask = this.mask;

      for (int i = offset; i < length ; i++) {
         byte curLetter = mapped[i];
         readingFrame = (readingFrame << knucleotide.BITS_PER_CHAR) | curLetter;
         readingFrame = readingFrame & mask;
         if (curLetter == letter) countCurrent(readingFrame);
      }

      this.readingFrame = readingFrame;
   }

   private void countCurrent(long readingFrame) {
      h.increment(readingFrame);
   }

   @Override
   public int getCount(int key) {
      return h.getCount(key);
   }

   @Override
   public int getCount(String key) {
      long lKey = knucleotide.toLongKey(key);
      return h.getCount(lKey);
   }
}

final class PackedHash {
   private static final float LOAD_FACTOR    = 0.75f;
   private static final int DEFAULT_SIZE    = 16;

   private static final long KEY_MASK      = 0x0000000fffffffffL; // 36 bits for k up to 18
   private static final long COUNT_MASK    = 0x000000000fffffffL; // should count up to 2^28-1 ~= 256m occurrences
   private   static final long KEY_SHIFT    = Long.numberOfLeadingZeros(KEY_MASK);

   long[] table = new long[DEFAULT_SIZE];

   int size = 0;
   int threshold = (int)(table.length*LOAD_FACTOR);

   public PackedHash() {}

   // borrowed from JDK
   static final int hash(int h) {
      h ^= (h >>> 20) ^ (h >>> 12);
      return h ^ (h >>> 7) ^ (h >>> 4);
   }

   public int getCount(long key) {
      int index = index(key, table);
      return getValue(table[index]);
   }

   public void increment(long key) {
      int index = index(key, table);
      if (table[index]++ == 0) {
         table[index] = pack(key, 1);
         size++;
         ensureCapacity();
      }
   }

   private void ensureCapacity() {
      if (size >= threshold) {
         long[] oldTable = this.table;
         long[] newTable = new long[oldTable.length*2];

         final int curSize = oldTable.length;
         for (int i = 0; i < curSize; i++) {
            long entry = oldTable[i];
            if (entry != 0) {
               //transfer
               long key = getKey(entry);
               int index = index(key, newTable);
               newTable[index] = entry;
            }
         }

         this.table = newTable;
         threshold = (int)(newTable.length * LOAD_FACTOR);
      }
   }

   private static int index(long key, long[] table) {
      int index = (int)(key ^ (key >>> 32));
      index = hash(index);

      final int indexMask = table.length - 1;
      index = index & indexMask;

      while ( ! (getKey(table[index]) == key || table[index] == 0) ) {
         index = (index + 1) & indexMask;
      }

      return index;
   }

   private static long getKey(long entry)          {return      (entry >>> KEY_SHIFT) & KEY_MASK;}
   private static int getValue(long entry)       {return (int)(entry  &  COUNT_MASK)          ;}
   private static long pack(long key, int value)    {return      (key   <<  KEY_SHIFT) | value   ;}
}

class AffinityDetectionHelper {
   public static boolean isLockedToSingleCore() {
      try {
         // when using a native method the effect of the JIT should be less unpredictable...
         double cpuWallTimeRatio = timeNativeMethod(4);

         // empirically determined to be ~ 0.85 with low variation when the
         // JVM is locked to a single core
         return cpuWallTimeRatio < 1.0;
      } catch (InterruptedException e) {
         throw new RuntimeException(e);
      }
   }

   private static double timeNativeMethod(int nThreads) throws InterruptedException {
      int workSize = 4;
      ArrayList<Thread> threads = new ArrayList<Thread>(nThreads);
      final int arraySize = 1024*1024;
      final byte[] a = new byte[arraySize];

      final AtomicLong cpuTime = new AtomicLong(0);

      ArrayList<ArrayBlockingQueue<Runnable>> queueList = new ArrayList<ArrayBlockingQueue<Runnable>>();
      for (int i = 0; i < nThreads; i++) {
         final ArrayBlockingQueue<Runnable> q = new ArrayBlockingQueue<Runnable>(workSize/nThreads);
         queueList.add(q);

         while (q.offer(new Runnable() {
            @Override
            public void run() {
               byte[] b = new byte[arraySize];
               System.arraycopy(a, 0, b, 0, a.length);
               cpuTime.addAndGet(ManagementFactory.getThreadMXBean( ).getCurrentThreadCpuTime());
            }
         })) {}

         final class T extends Thread {
            @Override
            public void run() {
               Runnable r;
               while ( (r = q.poll()) != null ) r.run();
            }
         }

         threads.add(new T());
      }

      long t0 = System.nanoTime();
      for (Thread t : threads) t.start();
      for (Thread t : threads) t.join();
      long wallTime = System.nanoTime() - t0;

      return (1.0*cpuTime.get()/wallTime);
   }
}
