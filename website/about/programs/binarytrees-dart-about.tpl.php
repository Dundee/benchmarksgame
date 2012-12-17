<p>"The Dart team has provided <a href="http://www.dartlang.org/articles/benchmarking/">an official benchmark harness</a> that ensures your benchmark follows the benchmarking procedures necessary for the Dart VM’s optimizer."</p>

<p>Here are the measurements reported from benchmark_harness.dart with n=12 n=16 n=20 on x64q</p>
<pre>Template(RunTime): 440000.0 us.
Template(RunTime): 11098000.0 us.
Template(RunTime): 1437800000.0 us.</pre>

<p>Which is actually --</p> 
<pre>0.44 secs
1.12 secs
143.78 secs</pre>
