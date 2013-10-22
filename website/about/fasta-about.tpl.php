<p><b>diff</b> program output N = 1000 with this 10KB <a href="iofile.php?test=<?=$SelectedTest;?>&amp;file=output">output file</a> to check your program is correct before contributing.
</p>

<p>We are trying to show the performance of various programming language implementations - so we ask that contributed programs not only give the correct result, but also <b>use the same algorithm</b> to calculate that result.</p>

<p>Each program should</p>
<ul>
<li>generate DNA sequences, by copying from a given sequence</li>
<li>generate DNA sequences, by weighted random selection from 2 alphabets
<ul>
<li>convert the expected probability of selecting each nucleotide into cumulative probabilities</li>
<li>match a random number against those cumulative probabilities to select each nucleotide (use linear search or binary search)</li>
<li>use this linear congruential generator to calculate a random number each time a nucleotide needs to be selected (don't cache the random number sequence)
<pre>

IM = 139968
IA = 3877
IC = 29573
Seed = 42
 	
Random (Max)
   Seed = (Seed * IA + IC) modulo IM
   = Max * Seed / IM
</pre>
</li>
</ul>
</li>
<li>write 3 sequences line-by-line in <a href="http://en.wikipedia.org/wiki/Fasta_format">FASTA format</a></li>
</ul>

<p>We'll use the generated FASTA file as input for other benchmarks (<a href="benchmark.php?test=revcomp&amp;lang=all">reverse-complement</a>, <a href="benchmark.php?test=knucleotide&amp;lang=all">k-nucleotide</a>).</p>

<p>Random DNA sequences can be based on a variety of <a href="http://www.scmbb.ulb.ac.be/pub/jvanheld/courses/regulatory_sequence_analysis/pdf_files/random_models.pdf">Random Models</a> (554KB pdf). You can use Markov chains or independently distributed nucleotides to <a href="http://rsat.scmbb.ulb.ac.be/rsat/random-seq_form.cgi">generate random DNA sequences online</a>.</p>
