<p>"The Dart team has provided <a href="http://www.dartlang.org/articles/benchmarking/">an official benchmark harness</a> that ensures your benchmark follows the benchmarking procedures necessary for the Dart VM’s optimizer."</p>

<p>Here are the measurements reported from benchmark_harness.dart with n=10 n=11 n=12 on x64q</p>
<pre>Template(RunTime): 4996000.0 us.
Template(RunTime): 64428000.0 us.
Template(RunTime): 897849000.0 us.</pre>

<p>Which is actually --</p> 
<pre>0.5 secs
6.44 secs
89.78 secs</pre>

<p>Compare those time measurements with these, and with measurements of the slightly modified <a href="program.php?test=fannkuchredux&lang=dart&id=2">fannkuch-redux Dart #2 program</a>.</p>
