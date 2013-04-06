<p>"The Dart team has provided <a href="http://www.dartlang.org/articles/benchmarking/">an official benchmark harness</a> that ensures your benchmark follows the benchmarking procedures necessary for the Dart VM’s optimizer."</p>

<p>Here are the measurements reported from benchmark_harness.dart with n=500000 n=5000000 n=50000000 on x64q</p>
<pre>Template(RunTime): 8295000.0 us.
Template(RunTime): 84912000.0 us.
Template(RunTime): 837669000.0 us.</pre>

<p>Which is actually --</p> 
<pre>0.83 secs
8.49 secs
83.77 secs</pre>
