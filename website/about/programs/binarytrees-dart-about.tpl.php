<p>"The Dart team has provided <a href="http://www.dartlang.org/articles/benchmarking/">an official benchmark harness</a> that ensures your benchmark follows the benchmarking procedures necessary for the Dart VM’s optimizer."</p>

<p>Here are the measurements reported from benchmark_harness.dart with n=12 n=16 n=20 on x64q</p>
<pre>Template(RunTime): 441800.0 us.
Template(RunTime): 10858000.0 us.
Template(RunTime): 1077458000.0 us.</pre>

<p>Which is actually --</p> 
<pre>0.44 secs
10.86.12 secs
107.748 secs</pre>
