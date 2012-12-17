<p>"The Dart team has provided <a href="http://www.dartlang.org/articles/benchmarking/">an official benchmark harness</a> that ensures your benchmark follows the benchmarking procedures necessary for the Dart VM’s optimizer."</p>

<p>Here are the measurements reported from benchmark_harness.dart with n=10 n=11 n=12 on x64q</p>
<pre>Template(RunTime): 4943000.0.
Template(RunTime): 63839000.0 us.
Template(RunTime): 889724000.0 us.</pre>

<p>Which is actually --</p> 
<pre>0.49 secs
6.38 secs
88.97 secs</pre>
