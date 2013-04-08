<p>"The Dart team has provided <a href="http://www.dartlang.org/articles/benchmarking/">an official benchmark harness</a> that ensures your benchmark follows the benchmarking procedures necessary for the Dart VM’s optimizer."</p>

<p>Here are the measurements reported from benchmark_harness.dart with n=10 n=11 n=12 on x64q</p>
<pre>Template(RunTime): 4638000.0 us.
Template(RunTime): 60076000.0 us.
Template(RunTime): 839693000.0 us.</pre>

<p>Which is actually --</p> 
<pre>0.46 secs
6.01 secs
83.97 secs</pre>

<p>Compare those time measurements with these, and with measurements of the slightly modified <a href="program.php?test=fannkuchredux&lang=dart&id=2">fannkuch-redux Dart #2 program</a>.</p>
