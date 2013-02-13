<?   // Copyright (c) Isaac Gouy 2004-2013 ?>


<p class="timestamp"><? printf('%s GMT', gmdate("d M Y, l, g:i a", $Changed)) ?></p>

<dl>


<dt><a href="#measure" name="measure">&nbsp;How programs are measured</a></dt>
<dd>
<dl>
<dt><a href="#process" name="process">The Process</a></dt>
<dd>
<ol>
<li>Each program is run and measured at the smallest input value, program output redirected to a file and compared to expected output. As long as the output matches expected output, the program is then run and measured at the next larger input value until measurements have been made at every input value.</li>

<li>If the program gives the expected output within an arbitrary cutoff time (120 seconds) the program is measured again (5 more times) with output redirected to /dev/null.</li>

<li>If the program doesn't give the expected output within an arbitrary timeout (usually one hour) the program is forced to quit. If measurements at a smaller input value have been successful within an arbitrary cutoff time (120 seconds), the program is measured again (5 more times) at that smaller input value, with output redirected to /dev/null.</li>

<li>The measurements shown on the website are either
<ul><li>within the arbitrary cutoff - the lowest time and highest memory use from 6 measurements</li>
<li>outside the arbitrary cutoff - the sole time and memory use measurement</li></ul></li>

<li>For sure, programs taking 4 and 5 hours were only measured once!</li>
</ol>
</dd>

<dt><a href="#time" name="time">How do you measure <strong>Time-used?</strong></a></dt>
<dd>
<p>Each program is run as a child-process of a Python script using <a href="http://docs.python.org/library/subprocess.html#popen-objects">Popen</a>.</p>
<ul>
<li><b>CPU&nbsp;secs</b>: The script child-process usr+sys rusage time is taken using <a href="http://docs.python.org/library/os.html?highlight=os.wait3#os.wait3">os.wait3</a><br /></li>
<li><b>Elapsed&nbsp;secs</b>: The time is taken before forking the child-process and after the child-process exits, using <a href="http://docs.python.org/library/time.html?highlight=time.time#time.time">time.time()</a></li>
</ul>
<p><strong>Time measurements include program startup time - see <a href="#java">&darr;&nbsp;What about Java&#174; VM warm-up?</a></strong></p>
<p><i>On win32 -</i></p>
<ul>
<li><i>CPU&nbsp;secs: QueryInformationJobObject(hJob,JobObjectBasicAccountingInformation) <a href="http://msdn.microsoft.com/en-us/library/ms684143(VS.85).aspx">TotalKernelTime+TotalUserTime</a><br /></i></li>
<li><i>Elapsed&nbsp;secs: The time is taken before forking the child-process and after the child-process exits, using <a href="http://msdn.microsoft.com/en-us/library/ms644904(VS.85).aspx">QueryPerformanceCounter</a></i></li>
</ul>
</dd>

<dt><a href="#memory" name="memory">How do you measure <strong>Memory-used?</strong></a></dt>
<dd><p>By sampling GLIBTOP_PROC_MEM_RESIDENT for the program and it's child processes every 0.2 seconds. Obviously those measurements are unlikely to be reliable for programs that run for less than 0.2 seconds.</p>
<p><i>On win32 - QueryInformationJobObject(hJob,JobObjectExtendedLimitInformation) <a href="http://msdn.microsoft.com/en-us/library/ms684156(VS.85).aspx">PeakJobMemoryUsed</a></i></p>
</dd>

<dt><a href="#gzbytes" name="gzbytes">How do you measure <strong>Code-used?</strong></a></dt>
<dd><p>We start with the source-code markup you can see, remove comments, remove duplicate whitespace characters, and then apply minimum GZip compression. The Code-used measurement is the size in bytes of that GZip compressed source-code file.</p>
<p>Thanks to Brian Hurt for the idea of using <em>size of compressed source code</em> instead of <em>lines of code</em>.</p>
<p>(Note: There is some evidence that <a href="http://my.safaribooksonline.com/book/software-engineering-and-development/9780596808310/general-principles-of-searching-for-and-using-evidence/herraiz_hassan_metrics">complexity metrics don't provide any more information than SLoC or LoC</a>.)</p>
</dd>

<dt><a href="#cpuload" name="cpuload">How do you measure <b>&asymp; CPU Load?</b></a></dt>
<dd><p>The GTop cpu idle and GTop cpu total are taken before forking the child-process and after the child-process exits, The percentages represent the proportion of cpu not-idle to cpu total for each core.</p>
<p><i>On win32 - GetSystemTimes <a href="http://msdn.microsoft.com/en-us/library/ms724400(VS.85).aspx">UserTime and IdleTime</a> are taken before forking the child-process and after the child-process exits. The percentage represents the proportion of TotalUserTime to UserTime+IdleTime (because that's like the percentage you'll see in Task Manager).</i></p>
</dd>


<dt><a href="#machine" name="machine">What hardware and OS do you measure the programs on?</a></dt>
<dd>
<p><b>Quad-core 2.4Ghz Intel<sup>&#174;</sup> Q6600<sup>&#174;</sup></b> with 4GB of RAM and 250GB SATA II disk drive.</p>
<p><b>Ubuntu&#8482; 12.10</b> Kernel Linux 3.2.0-35-generic</p>
</dd>


</dl>
</dd>




<dt><a href="#faqs" name="faqs">&nbsp;FAQs</a></dt>
<dd>
<dl>

<dt><a href="#languagex" name="languagex">Why don't you include language X?</a><br />Why don't you include my favorite implementation of language X?<br />Why don't you include Microsoft&#174; Windows&#174;?</dt>
<dd>
<p>Because I want to do fewer chores not more!</p>

If you're interested in something not shown then <a href="https://alioth.debian.org/snapshots.php?group_id=100815" title="Download the nightly snapshot"><b>please</b> take the program source code</a> and the measurement scripts and <b>publish your own measurements</b>.</p> 

<p>The Python script "bencher does repeated measurements of program cpu time, elapsed time, resident memory usage, cpu load while a program is running, and summarizes those measurements" - <a href="<?=DOWNLOAD_PATH;?>bencher.zip"><b>download bencher</b></a> and unzip into your ~ directory, check the requirements and <a href="http://www.andre-simon.de/doku/highlight/en/highlight.html" title="highlight, ndiff">recommendations</a>, and read the license before use.</p>
<p>(As an alternative, you should take a look at these Python measurement scripts designed for statistically rigorous Java performance evaluation - <a href="http://www.elis.ugent.be/JavaStats"><b>JavaStats</b></a>.)</p>
</dd>


<dt><a href="#contest" name="contest">Why don't you accept every program that gives the correct result?</a></dt>
<dd><p>We are trying to show the performance of various programming language implementations - so we ask that contributed programs not only give the correct result, but also <b>use the same algorithm</b> to calculate that result.</p>
<p>We do show one contest where you can use different algorithms - <a href="./u64q/performance.php?test=meteor">meteor-contest</a>.</p>
<p>If the point was to compare different algorithms then it would be appropriate to do something like the <a href="http://www.cs.cmu.edu/~pbbs/">Problem Based Benchmark Suite</a>.</p>
</dd>



<dt><a href="#java" name="java">What about Java&#174; VM warm-up?</a></dt>
<dd><p>Let's see how much, or how little, the time taken to invoke the JVM might contribute to the usual Java program times shown in the benchmarks game. Here are some <b>additional</b> (Intel&#174; Q6600&#174; quad-core) elapsed time measurements, taken after the Java programs started and before they exited.</p> 

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
</dd>



<dt><a href="#inputvalue" name="inputvalue">What does N mean?</a></dt>
<dd><p>N means the value passed to the program on the command-line (or the value used to create the data file passed to the program on stdin). Larger N causes the program to do more work - mostly measurements are shown for the largest N, the largest workload.</p>
<p>Read <a href="#measure">&darr;&nbsp;How programs were measured</a></p>
</dd>

<dt><a href="#cpuloadpercent" name="cpuloadpercent">What does '27% 34% 28% 67%' &asymp; CPU Load mean?</a></dt>
<dd><p>When the program was being measured: the first core was not-idle about 27% of the time, the second core was not-idle about 34% of the time, the third core was not-idle about 28% of the time, the fourth core was not-idle about 67% of the time.</p>
<p>When <em>all the programs</em> show &asymp; CPU Load like this '0% 0% 0% 100%' you are probably looking at measurements of programs forced to use just one core - the fourth core (rather than being allowed to use any or all of the CPU cores).</p>
<p>Read <a href="#cpuload">&darr;&nbsp;How did you measure &asymp; CPU Load?</a></p>
</dd>


<dt><a href="#suffixes" name="suffixes">What do #2 #3 mean?</a></dt>
<dd><p>Nothing - they are arbitrary suffixes that identify a specific program.</p>
</dd>


</dl>
</dd>



<dt><a href="#contribute" name="contribute">&nbsp;How to contribute programs</a></dt>
<dd>
<dl>


<dt><a href="#diff" name="diff">How much effort should I put into getting the program correct?</a></dt>
<dd><p>Do design-iteration on your own computer, or in a language newsgroup. Only contribute programs which give correct results - <b>diff the program output with the provided output file before you contribute the program</b>.</p>
</dd>

<dt><a href="#implement" name="implement">How should I implement programs?</a></dt>
<dd><p>Prefer plain vanilla programs - after all we're trying to compare language implementations not programmer effort and skill. We'd like your programs to be easily viewable - so please format your code to fit in <b>less than 80 columns</b> (we don't measure lines-of-code!).</p>
<p>We are trying to show the performance of various programming language implementations - so we ask that contributed programs not only give the
correct result, but also <b>use the same algorithm</b> to calculate that result.</p>
<p>We do show one contest where you can use different algorithms - <a href="./u64q/performance.php?test=meteor">meteor-contest</a>.</p>
</dd>

<dt><a href="#unroll" name="unroll">How should I implement loops?</a></dt>
<dd><p>Don't manually unroll loops!</p></dd>

<dt><a href="#input" name="input">How should I implement data-input?</a></dt>
<dd><p>Programs are measured across a range of input-values; programs are expected to either take a single command-line parameter or read text from <b>stdin</b>.</p>
<p>(Look at what the other programs do.)</p>
</dd>

<dt><a href="#output" name="output">How should I implement data-output?</a></dt>
<dd><p>Programs should write to <b>stdout</b>. Program output is redirected to a log-file and diff'd with the expected output.</p>
<p>(Look at what the other programs do.)</p>
</dd>

<dt><a href="#credit" name="credit">How should I identify my program?</a></dt>
<dd><p>Include a header comment in the program like this:</p>
<pre>
/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by &#8230;
   modified by &#8230;
*/
</pre>
</dd>


<dt><a href="#stepbystep" name="stepbystep">Finally! <b>Use the Tracker to contribute programs</b></a></dt>

<dd><p>Attach the full source-code file of a tested program. Please don't paste source-code into the description field. Please don't contribute patch-files.</p>
<p>Before contributing programs</p>
<ul>
<li><b>debian issue their own security certificate</b> - your web browser will complain.</li>
<li>read and accept the <a href="license.php" title="Read the revised BSD license"><b>Revised&nbsp;BSD&nbsp;license</b></a> - all contributed programs are published under this revised BSD license.</li>
<li><a href="http://alioth.debian.org/account/register.php"><b>create an Alioth ID</b></a> and login.</li>
</ul>

<p>The Tracker</p>
<ul>
<li>After login, go to the <a href="https://alioth.debian.org/tracker/?func=browse&group_id=100815&atid=413122"><b>"Play the Benchmarks Game" Tracker</b></a></li>
<li>Find and click the "Play the Benchmarks Game: <b>Submit New</b>" link</li>
<li>Now start from the bottom of the form and work your way up</li>
</ul>

<p>Start from the bottom</p>
<ol>
<li><b>Attach</b> the program source-code file - do this first because it's easy to forget.</li>
<li>Say in the <b>Detailed description</b> how this program fixes an error or is faster or was missing or &#8230; Give us reasons to accept your program.</li>
<li>Each <b>Summary</b> text <em><b>must</b></em> be unique! Follow this convention:<br />
language, benchmark, your-name, date, (version)<br />
<em>Ruby nsieve Glenn Parker 2005-03-28</em><br />
</li>
<li><b>Language Implementation</b>: select the language implementation</li>
<li><b>Task</b>: select the benchmark</li>
<li>click the Submit button</li>
</ol>

</dd>

<dt><a href="#track" name="track">How can I track what happens to the program I contributed?</a></dt>
<dd>

<p>You created an <a href="#aliothid">&darr;&nbsp;Alioth ID</a> with a valid email address so you'll receive email updates when your program is accepted and measured.</p>
</dd>

</dl>
</dd>




<dt><a href="#misc" name="misc">&nbsp;What&#8230;? Where&#8230;? Why&#8230;?</a></dt>
<dd>

<p>Please <a href="http://alioth.debian.org/account/register.php"><b>create an Alioth ID</b></a>, login and ask your questions in <a href="https://alioth.debian.org/forum/forum.php?forum_id=2965" title="Share Opinions, Find Help"><b>the discussion forum</b></a>.</p>
<p>Note: <b>Debian issue their own security certificate</b> - your web browser will complain.</p>
</dd>

</dl>
</dd>


</dl>

