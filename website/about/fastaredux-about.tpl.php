<p><b>diff</b> program output N = 1000 with this 10KB <a href="iofile.php?test=<?=$SelectedTest;?>&amp;file=output">output file</a> to check your program is correct before contributing.
</p>

<p>We are trying to show the performance of various programming language implementations - so we ask that contributed programs not only give the correct result, but also <b>use the same algorithm</b> to calculate that result.</p>

<p>Each program should</p>
<ul>
<li>generate DNA sequences, by copying from a given sequence</li>
<li>generate DNA sequences, by weighted random selection from 2 alphabets
<ul>
<li>convert the expected probability of selecting each nucleotide into cumulative probabilities</li>
<li>encode the cumulative probabilities as blocks of duplicate lookup-table items</li>
<li>select an initial lookup-table item using a random number as an approximate index</li>
<li>match that random number against the cumulative probabilities to select the nucleotide</li>
<li>use this linear congruential generator to calculate a random number each time a nucleotide needs to be selected (don't cache the random number sequence)
<pre>

IM = 139968
IA = 3877
IC = 29573
Seed = 42
 	
Random ()
   Seed = (Seed * IA + IC) modulo IM
   = Seed / IM
</pre>
</li>
</ul>
</li>
<li>write 3 sequences line-by-line in <a href="http://en.wikipedia.org/wiki/Fasta_format">FASTA format</a></li>
</ul>
