<p><g:plusone annotation="none"></g:plusone></p>

<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<dl>
<dt><a href="#jump" name="jump">&nbsp;<strong>And please don't jump to conclusions!</strong></a></dt>
<dd>
<dl>

<dd>
<blockquote><p>"The performance of a benchmark, even if it is derived from a real program, may not help to predict the performance of similar programs that have different hot spots." <br/><a href="http://www.larcenists.org/Twobit/bmcrock.temp.html">Benchmarks are a crock</a></p></blockquote>

<blockquote><p>"It may seem paradoxical to use an interpreted language in a high-throughput environment, but we have found that the CPU time is rarely the limiting factor; the expressibility of the language means that most programs are small and spend most of their time in I/O and native run-time code." <br/><a href="http://research.google.com/archive/sawzall-sciprog.pdf">Interpreting the Data: Parallel Analysis with Sawzall</a> page 27 pdf</p></blockquote>

<blockquote><p>"We measure three specific areas of JavaScript runtime behavior: 1) functions and code; 2) heap-allocated objects and data; 3) events and handlers. We find that the benchmarks are <b>not representative</b> of many real websites and that conclusions reached from measuring the benchmarks may be misleading." <br/><a href="http://research.microsoft.com/en-us/projects/jsmeter/">JSMeter: Characterizing Real-World Behavior of JavaScript Programs</a></p></blockquote>

<blockquote><p>"Would you believe us if we told you: “we can predict the benefit of our optimization, <i>O</i>, by evaluating it in one or a few experimental setups using a handful of benchmarks?” Again, you should not: we all know that computer systems are highly sensitive and there is no reason to believe that the improvement with <i>O</i> is actually due to <i>O</i>; it may be a result of a biased experimental setup." <br/><a href="http://citeseer.ist.psu.edu/viewdoc/summary?doi=10.1.1.163.8395">Producing Wrong Data Without Doing Anything Obviously Wrong!</a></p></blockquote>

</dd>
<dt><a href="#app" name="app"><b>Your application is </b><strong>the ultimate benchmark</strong></a></dt>

<dd>
<blockquote><p>"In order to find the optimal cost/benefit ratio, Wirth used a highly intuitive metric, the origin of which is unknown to me but that may very well be Wirth's own invention. He used <b>the compiler's self-compilation speed</b> as a measure of the compiler's quality. Considering that Wirth's  compilers were written in the languages they compiled, and that compilers are substantial and non-trivial pieces of software in their own right, this introduced a highly practical benchmark that directly contested a compiler's complexity against its performance. Under the self compilation speed benchmark, only those optimizations were allowed to be incorporated into a compiler that accelerated it by so much that the intrinsic cost of the new code addition was fully compensated."</p><p><a href="http://www.ics.uci.edu/~franz/Site/pubs-pdf/BC03.pdf">Oberon: The Overlooked Jewel</a> (pdf) Michael Franz, in L. Boszormenyi, J. Gutknecht, G. Pomberger "The School of Niklaus Wirth" 2000.
</p></blockquote>
</dd>

</dl>
</dd>



<dt><a href="#comparison" name="comparison">&nbsp;<strong>Apples and Oranges</strong></a></dt>
<dd>
<dl>
<dd>
<p>Programming languages are compared against each other as though their designers intended them to be used for the exact same purpose - that just isn't so.</p>

<blockquote><p>"Lua is a tiny and simple language, partly because it does not try to do what C is already good for, such as sheer performance, low-level operations, or interface with third-party  software. Lua relies on C for those tasks."<br />
<a href="http://www.inf.puc-rio.br/~roberto/book/">Programming in Lua</a>, preface
</p></blockquote>

<blockquote><p>"Most (all?) large systems developed using Erlang make heavy use of C for low-level code, leaving Erlang to manage the parts which tend to be complex in other languages, like controlling systems spread across several machines and implementing complex protocol logic."<br />
<a href="http://www.erlang.org/faq/introduction.html#id49576">Frequently Asked Questions about Erlang</a>
</p></blockquote>


<p>The difficulty is that programming languages (and programming language implementations) are <b>more different than apples and oranges</b>, but the question is still asked - Will my program be faster if I write it in language X? - and there's still a wish for a simpler answer than - It depends how you write it!</p>
</dd>
</dl>
</dd>


<dt><a href="#effort" name="effort">&nbsp;<strong>Programmer skill and effort</strong></a></dt>
<dd>
<dl>

<dd>
<p>No attempt has been made to assess whether programs contributed for a particular language were consistently the work of more highly skilled programmers than programs contributed for other languages. The source code comments show that some programs were contributed by core developers of the programming language implementation - world renowned expert programmers. It's fair to say that other programs were contributed by programmers who are less skilled.</p>
<p>No attempt has been made to assess whether programs contributed for a particular language were consistently worked on longer and harder than programs contributed for other languages. As a very crude indication of how that may vary, look at which languages have had more programs contributed and which have had fewer programs contributed (than an even split).</p>

<table>
<tr><th colspan="2">% programs contributed 2005-2010, by language</th></tr>
<tr><td>Python</td><td>12.2</td></tr>
<tr><td>C++</td><td>9.0</td></tr>
<tr><td>C</td><td>7.1</td></tr>
<tr><td>Haskell</td><td>7.1</td></tr>
<tr><td>Java</td><td>6.4</td></tr>
<tr><td>Pascal</td><td>6.2</td></tr>
<tr><td>Perl</td><td>5.8</td></tr>
<tr><td>Lisp</td><td>5.0</td></tr>
<tr><td>OCaml</td><td>4.2</td></tr>
<tr><td>Ruby</td><td>4.1</td></tr>
<tr><td>==</td><td>&nbsp;</td></tr>
<tr><td>Lua</td><td>3.5</td></tr>
<tr><td>Erlang</td><td>3.5</td></tr>
<tr><td>Scala</td><td>3.2</td></tr>
<tr><td>Ada</td><td>2.9</td></tr>
<tr><td>ATS</td><td>2.9</td></tr>
<tr><td>Scheme</td><td>2.9</td></tr>
<tr><td>C#</td><td>2.4</td></tr>
<tr><td>F#</td><td>2.0</td></tr>
<tr><td>Fortran</td><td>1.6</td></tr>
<tr><td>Clojure</td><td>1.5</td></tr>
<tr><td>JavaScript</td><td>1.5</td></tr>
<tr><td>PHP</td><td>1.5</td></tr>
<tr><td>Smalltalk</td><td>1.0</td></tr>
<tr><td>Go</td><td>1.0</td></tr>
</table>

<p>Some source code comments show that programs were translated from one language to a similar language - the programmer skill and effort was transfered to programs written in a different language.</p>
</dd>

</dl>
</dd>



<dt><a href="#resource" name="resource">&nbsp;<strong>A very modest resource</strong></a></dt>
<dd>
<dl>


<dd>
<p>Quite unexpectedly, some of the tasks shown on the benchmarks game website have become a very modest resource for programming language researchers. For example&nbsp;-</p>
<ul>
<li><a href="http://www.lirmm.fr/~ducour/Doc-objets/ECOOP2012/APPLC/geneticsandcode_paper_2.pdf">Code Generator Amelioration Using Genetic Algorithm Techniques</a> pdf</li> 
<li><a href="http://tratt.net/laurie/research/publications/papers/bolz_tratt__the_impact_of_metatracing_on_vm_design_and_implementation.pdf">The Impact of Meta-Tracing on VM Design and Implementation</a> pdf</li> 
<li><a href="http://www.ccs.neu.edu/racket/pubs/oopsla12-stf.pdf">Optimization Coaching: Optimizers Learn to Communicate with Programmers</a> pdf</li> 
<li><a href="http://www.slideshare.net/jandot/l-fu-dao-a-novel-programming-language-for-bioinformatics">Dao: a novel programming language for bioinformatics</a> slides</li> 
<li><a href="http://safari.ece.cmu.edu/MSPC2012/papers/p58-davis.pdf">Towards Region-Based Memory Management for Go</a> pdf</li> 
<li><a href="http://soft.vub.ac.be/~smarr/downloads/tools12-smarr-dhondt-identifying-a-unifying-mechanism-for-the-implementation-of-concurrency-abstractions-on-multi-language-virtual-machines.pdf">Identifying A Unifying Mechanism for the Implementation of Concurrency Abstractions on Multi-Language Virtual Machines</a> pdf</li> 
<li><a href="http://www.cs.rice.edu/~vsarkar/PDF/ipdps09-accumulators-final-submission.pdf">Phaser Accumulators: a New Reduction Construct for Dynamic Parallelism</a> pdf</li> 
<li><a href="http://www.scss.tcd.ie/publications/tech-reports/reports.09/TCD-CS-2009-37.pdf">Dynamic Interpretation for Dynamic Scripting Languages</a> pdf</li>  
<li><a href="http://www.softlab.ntua.gr/research/techrep/CSD-SW-TR-8-09.pdf">Race-free and Memory-safe Multithreading: Design and Implementation in Cyclone</a> pdf</li>  
<li><a href="http://moscova.inria.fr/~zappa/projects/liketypes/paper.pdf">Integrating Typed and Untyped Code in a Scripting Language</a> pdf</li>  
<li><a href="http://www.springerlink.com/index/P4U0851W34180N74.pdf">Inline Caching Meets Quickening</a> pdf</li> 
</ul>
<p>Having a collection of programs that implement the same tasks in different programming languages is at least convenient. Presumably convenience is why Bjarne Stroustrup refers to the benchmarks game (to support his p
    From: Matthew Wilson <diakopter@gmail.com>
    To: Isaac Gouy <igouy2@yahoo.com>
    Sent: Friday, March 1, 2013 12:32 PM
    Subject: paper using shootout game

    maybe you are interested..
    http://www.lirmm.fr/~ducour/Doc-objets/ECOOP2012/APPLC/geneticsandcode_paper_2.pdf

oint about run time typing) in <a href="http://www.stroustrup.com/Software-for-infrastructure.pdf">"Software Development for Infrastructure"</a> pdf, IEEE Computer, January 2012.</p>
</dd>

<dd>
<p>The Go programming language distribution includes Go programs for the tasks shown on the benchmarks game website, and those programs are compiled during install&nbsp;-</p>
<pre>
    --- cd ../test/bench
    fasta
    reverse-complement
    nbody
    binary-tree
    binary-tree-freelist
    fannkuch
    fannkuch-parallel
    regex-dna
    regex-dna-parallel
    spectral-norm
    k-nucleotide
    k-nucleotide-parallel
    mandelbrot
    meteor-contest
    pidigits
    threadring
    chameneosredux
</pre>

<p>Similarly, <a href="https://github.com/mozilla/rust/tree/master/src/test/bench">mozilla/<b>rust</b>/src/test/bench</a></p>

<p>Several of the tasks had already been used as benchmarks and were adopted almost unchanged for the benchmarks game, for example - fannkuch (now fannkuch-redux) and binary-trees. Similarly, many of the tasks from the benchmarks game have been adopted by other projects - for example, 10 of the 26 WebKit SunSpider JavaScript tests.</p>
</dd>
</dl>
</dd>



<dt><a href="#history" name="history">&nbsp;<strong>A Decade in the Making</strong></a></dt>
<dd>
<dl>
<dd>
<p>Once upon a time, Doug Bagley had a burst of crazy curiosity&nbsp;-</p>
<blockquote><p>"When I started this project, my goal was to compare all the major scripting languages. Then I started adding in some compiled languages for comparison ... and it's still growing with no end in sight. I'm doing it so that I can learn about new languages, compare them in various (possibly meaningless) ways, and most importantly, have some fun. ... By the way, the word Great in the title refers to quantity, not quality (I will let the reader judge that)."</p></blockquote>
<p><a href="http://web.archive.org/web/*/http://www.bagley.org/~doug/shootout/">The Internet Archive preserves some of those web pages</a> abandoned in early 2002&nbsp;-</p>
<blockquote><p>"Hi, the shootout is an unfinished project. I've decided to discontinue updates to it for now while I work on some other things. Thanks for everyone's help."</p></blockquote>
<p>Aldo Calpini ported Doug Bagley's "Great Computer Language Shootout" to Win32 but there have been <a href="http://dada.perl.it/shootout/">no updates since 2003</a>.</p>
<p>2004 saw another revival, this time by Brent Fulgham&nbsp;-</p>
<blockquote><p>"The project goals have not changed substantially since Doug's original project. This work is continuing so that we all can learn about new languages, compare them in various (possibly meaningless) ways, and most importantly, have some fun!"</p></blockquote>
<p>Brent Fulgham chose to host the project at alioth.debian.org (like sourceforge or savannah but a service for Debian Developers). Sometimes that seemed to confuse people into thinking <a href="http://web.archive.org/web/20040611035744/http://shootout.alioth.debian.org/">"the Great Computer Language Shootout"</a> was an integral part of Debian rather than a tiny independent project.</p>
<p>Although Brent Fulgham started off with those same programs from Doug Bagley's Great Computer Language Shootout, new benchmark tasks were created and those Doug Bagley programs were replaced during 2005. Isaac Gouy designed a new website and started making performance measurements on Gentoo Linux Intel® Pentium® 4 and took on the day-to-day admin work.</p>
<p>The Virginia Tech shooting in April 2007 once again pushed gun violence into the media headlines. <strong>There was no wish to be associated with or to trivialise the slaughter behind the phrase shootout so the project was renamed back on 20th April 2007 -<a title="" href="http://benchmarksgame.alioth.debian.org/">&nbsp;The Computer Language Benchmarks Game</a></strong>.</p>
<p>By 2008 Brent had stopped updating the Debian Linux AMD™ Sempron™ measurements and moved on to other projects.</p>
</dd>

<dt><a href="#multicore" name="multicore">&nbsp;<strong>x64 and multi-core</strong></a></dt>
<dd>
<p>By 2008 new computer hardware was commonly dual-core or quad-core; by 2008 operating systems were often 64 bit - so the benchmarks game was moved to a new quad-core Intel® Q6600® machine and <strong>4 sets of measurements</strong> were recorded:</p>
<ul>
<li><p><span class="u32"><a title="" href="./u32/which-programs-are-fastest.php">&nbsp;Ubuntu Linux x86 programs bound to a single core&nbsp;</a></span></p></li>
<li><p><span class="u32q"><a title="" href="./u32q/which-programs-are-fastest.php">&nbsp;Ubuntu Linux x86 programs enabled for quad core&nbsp;</a></span></p></li>
<li><p><span class="u64"><a title="" href="./u64/which-programs-are-fastest.php">&nbsp;Ubuntu Linux x64 programs bound to a single core&nbsp;</a></span></p></li>
<li><p><span class="u64q"><a title="" href="./u64q/which-programs-are-fastest.php">&nbsp;Ubuntu Linux x64 programs enabled for quad core&nbsp;</a></span></p></li>
</ul>
<p>That was a lot more work.</p>
</dd>

<dt><a href="#freshness" name="freshness">&nbsp;<strong>Fresh! Fresh! Fresh!</strong></a></dt>
<dd>
<p>Since September 2008 new measurements have been recorded and published more than 1000 times. Fresh measurements are usually published several times a week in response to some trigger event - a new program was contributed, a new version of a programming language implementation was released, a new version of Ubuntu was released.</p>
<p><img src="<?=IMAGE_PATH;?>fresh_png_29may2012.php"
   alt=""
   title=""
   width="400" height="225"
 /></p>
<p>Although up-to-date content has been a priority and new measurements are made soon after new versions of a programming implementation are released - that isn't enough.</p>
<p>Just like any other long running software project, some of the measured programs will be <i>legacy code</i> that hasn't been updated to take advantage of the latest improvements in the language implementation. Just like any other long running software project, there's a time lag between the latest improvements in the language implementation and understanding how those improvements can be used, and overcoming community fatigue to contribute programs that use those improvements.</p>

<ul>
<li><p>Unique Visitors, October through September 2011 - 319,829</p></li>
<li><p>Unique Visitors, October through September 2012 - 340,917</p></li>
</ul>
</dd>

</dl>
</dd>



<dt><a href="#lastwords" name="lastwords">&nbsp;<strong>Last words -- A good starting point</strong></a></dt>
<dd>
<dl>

<dd>
<blockquote><p>"<strong>How does Java compare in terms of speed to C or C++ or C# or Python?</strong> The answer depends greatly on the type of application you're running. No benchmark is perfect, but Computer Language Benchmarks Game is a good starting point." <br/><a href="http://algs4.cs.princeton.edu/faq/">Algorithms, 4th Edition - FAQ</a></p></blockquote>
</dd>


</dl>
</dd>


</dl>

