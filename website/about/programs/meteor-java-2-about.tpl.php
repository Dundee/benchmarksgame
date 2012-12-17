<p>The actual program run time is so short, that virtually all the measured time is <em>overhead</em> - JVM startup, class loading, Hotspot adaptive compilation,...</p>
<p>Compare to "Warmed" JVM timings using System.nanoTime() - see <a href="<?=CORE_SITE;?>help.php#java">Help: What about Java?</a></p>
<pre>
Flat profile of 0.16 secs (16 total ticks): main

  Interpreted + native   Method                        
  6.2%     0  +     1    java.lang.Throwable.fillInStackTrace
  6.2%     0  +     1    Total interpreted

     Compiled + native   Method                        
 50.0%     5  +     3    meteor$Board.genAllSolutions
 25.0%     0  +     4    meteor$Soln.<init>
 12.5%     1  +     1    meteor$Board.hasBadIslands
  6.2%     0  +     1    meteor$Board.badRegion
 93.8%     6  +     9    Total compiled


Flat profile of 0.00 secs (1 total ticks): DestroyJavaVM

  Thread-local ticks:
100.0%     1             Blocked (of total)


Global summary of 0.17 seconds:
100.0%    17             Received ticks
117.6%    20             Compilation
</pre>
