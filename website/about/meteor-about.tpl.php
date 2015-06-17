<p>This is a <b>contest</b> - different algorithms may be used.</p>

<p>You are expected to <b>diff the output from your program N = 2098 against this </b><a href="iofile.php?test=<?=$SelectedTest;?>&amp;file=output"><b>output file</b></a> <b><em>before</em> you contribute your program.</b></p>


<p>The <a href="http://devve.com/tutorials/Server-Side-Coding/Java/optimize-java-app/page1.html"><b>Meteor Puzzle</b></a> board is made up of 10 rows of 5 hexagonal Cells. There are 10 puzzle pieces to be placed on the board, we'll number them 0 to 9. Each puzzle piece is made up of 5 hexagonal Cells. As different algorithms may be used to generate the puzzle solutions, we require that the solutions be printed in a standard order and format. Here's one approach - working along each row left to right, and down the board from top to bottom, take the number of the piece placed in each cell on the board, and create a string from all 50 numbers, for example the smallest puzzle solution would be represented by </p><pre>00001222012661126155865558633348893448934747977799</pre>
<p>Print the smallest and largest Meteor Puzzle 50 character solution string in this format to mimic the hexagonal puzzle board:</p>

<pre>0 0 0 0 1 
 2 2 2 0 1 
2 6 6 1 1 
 2 6 1 5 5 
8 6 5 5 5 
 8 6 3 3 3 
4 8 8 9 3 
 4 4 8 9 3 
4 7 4 7 9 
 7 7 7 9 9 
</pre>

<p>The command line parameter N should limit how many solutions will be found before the program halts, so that you can work with just a few solutions to debug and optimize your program.</p>
<p>Diff program output N = 2098 against the <a href="iofile.php?test=<?=$SelectedTest;?>&amp;file=output">output file</a> to check the format is correct.</p>

<p><b>The Meteor Puzzle and 3 Java puzzle solvers</b> are described in <a href="http://www.dsc.ufcg.edu.br/~jacques/cursos/2004.2/gr/recursos/j-javaopt.pdf"><b>"Optimize your Java application's performance"</b> (pdf)</a>.</p>

