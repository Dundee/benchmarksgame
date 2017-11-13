<?=$Version;?>
<p>Home Page: <a href="http://www.oracle.com/technetwork/java/javase/overview/index.html">Java SE at a Glance</a></p>
<p>Download: <a href="http://www.oracle.com/technetwork/java/javase/downloads/index.html">Java SE Downloads</a></p>


<p>Let's see how much, or how little, the time taken to invoke the JVM might contribute to the usual Java program times shown in the benchmarks game. Here are some <b>additional</b> (Intel&#174; i5-7200U&#174; dual-core) elapsed time measurements, taken after the Java programs started and before they exited.</p>

<p>In the first case (Cold), we simply started and measured the program 66 times; and then discarded the first measurement leaving 65 data points.</p>

<pre>
   public static void main(String[] args){
      for (int i=0; i&lt;1; ++i){
         System.gc();
         long t1 = System.nanoTime();
         nbody.program_main(args);
         long t2 = System.nanoTime();
         System.err.println( String.format( "%.6f", (t2 - t1) * 1e-9 ) );
      }
   }
</pre>

<p>In the second case (Warmed), we started the program once and repeated measurements again and again and again 66 times without restarting the JVM; and then discarded the first measurement leaving 65 data points.</p>

<pre>
   public static void main(String[] args){
      for (int i=0; i&lt;66; ++i){
         System.gc();
         long t1 = System.nanoTime();
         nbody.program_main(args);
         long t2 = System.nanoTime();
         System.err.println( String.format( "%.6f", (t2 - t1) * 1e-9 ) );
      }
   }
</pre>

<p>Compare these additional measurements against the <b>usual</b> Java program measurements shown in the benchmarks game --</p>

<table>
<tr>
<th colspan="7">"1.7.0_06"&nbsp;Java HotSpot(TM) 64-Bit Server VM</th>
</tr>

<tr>
<th>System.nanoTime()</th>
<th colspan="2">&nbsp;1)&nbsp;Cold&nbsp;</th>
<th colspan="2">&nbsp;2)&nbsp;Warmed&nbsp;</th>
<th colspan="2">&nbsp;</th>
</tr>

<tr>
<th>&nbsp;</th>
<th class="num">mean</th>
<th class="num">&#963;</th>
<th class="num">mean</th>
<th class="num">&#963;</th>
<th class="num">&nbsp;&nbsp;<b>usual</b></th>
</tr>

<tr>
<td>meteor&nbsp;contest&nbsp;&nbsp;</td>
<td>0.0118s</td>
<td>0.0007</td>
<td>0.0016s</td>
<td>0.0002</td>
<td>0.22s</td>
</tr>

<tr>
<td>fasta-redux&nbsp;&nbsp;</td>
<td>2.45s</td>
<td>0.00</td>
<td>2.32s</td>
<td>0.00</td>
<td>2.51s</td>
</tr>

<tr>
<td>spectral-norm&nbsp;&nbsp;</td>
<td>4.44s</td>
<td>0.02</td>
<td>4.20s</td>
<td>0.16</td>
<td>4.51s</td>
</tr>

<tr>
<td>pidigits&nbsp;&nbsp;</td>
<td>4.69s</td>
<td>0.09</td>
<td>4.44s</td>
<td>0.05</td>
<td>4.61s</td>
</tr>

<tr>
<td>fasta&nbsp;&nbsp;</td>
<td>5.07s</td>
<td>0.46</td>
<td>4.84s</td>
<td>0.02</td>
<td>5.13s</td>
</tr>

<tr>
<td>chameneos-redux&nbsp;&nbsp;</td>
<td>5.84s</td>
<td>0.46</td>
<td>5.70s</td>
<td>0.48</td>
<td>5.65s</td>
</tr>

<tr>
<td>mandelbrot&nbsp;&nbsp;</td>
<td>7.93s</td>
<td>0.23</td>
<td>7.99s</td>
<td>0.01</td>
<td>7.02s</td>
</tr>

<tr>
<td>k-nucleotide&nbsp;&nbsp;</td>
<td>8.09s</td>
<td>0.28</td>
<td>&nbsp;--&nbsp;</td>
<td>&nbsp;--&nbsp;</td>
<td>8.05s</td>
</tr>

<tr>
<td>regex-dna&nbsp;&nbsp;</td>
<td>8.65s</td>
<td>0.27</td>
<td>&nbsp;--&nbsp;</td>
<td>&nbsp;--&nbsp;</td>
<td>8.61s</td>
</tr>

<tr>
<td>binary-trees&nbsp;&nbsp;</td>
<td>10.54s</td>
<td>0.28</td>
<td>7.66s</td>
<td>0.16</td>
<td>9.08s</td>
</tr>

<tr>
<td>fannkuch-redux&nbsp;&nbsp;</td>
<td>16.89s</td>
<td>1.32</td>
<td>17.26s</td>
<td>0.10</td>
<td>17.38s</td>
</tr>

<tr>
<td>nbody&nbsp;&nbsp;</td>
<td>22.43s</td>
<td>0.00</td>
<td>22.41s</td>
<td>0.00</td>
<td>22.50s</td>
</tr>

<tr>
<td>binary-trees-redux&nbsp;&nbsp;</td>
<td>34.15s</td>
<td>0.39</td>
<td>33.93s</td>
<td>0.31</td>
<td>33.38s</td>
</tr>

</table>

<p>The largest and most obvious effects of bytecode loading and dynamic optimization can be seen with the meteor-contest program which only runs for a fraction of a second.</p>
