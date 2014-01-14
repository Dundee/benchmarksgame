# The Computer Language Benchmarks game
#  http://benchmarksgame.alioth.debian.org/
#
#  contributed by Barry Walsh
#  optimized by Mykola Zubach

# port of fasta.rb #6 

use strict;
use warnings;
use feature 'say';

use constant SEQ_WIDTH => 60;

use constant IM => 139968;
use constant IA => 3877;
use constant IC => 29573;

my $LAST = 42;

my $alu =
   'GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG' .
   'GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA' .
   'CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT' .
   'ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA' .
   'GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG' .
   'AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC' .
   'AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA';

my $iub = [
   [ 'a', 0.27 ],
   [ 'c', 0.12 ],
   [ 'g', 0.12 ],
   [ 't', 0.27 ],
   [ 'B', 0.02 ],
   [ 'D', 0.02 ],
   [ 'H', 0.02 ],
   [ 'K', 0.02 ],
   [ 'M', 0.02 ],
   [ 'N', 0.02 ],
   [ 'R', 0.02 ],
   [ 'S', 0.02 ],
   [ 'V', 0.02 ],
   [ 'W', 0.02 ],
   [ 'Y', 0.02 ]
];

my $homosapiens = [
   [ 'a', 0.3029549426680 ],
   [ 'c', 0.1979883004921 ],
   [ 'g', 0.1975473066391 ],
   [ 't', 0.3015094502008 ]
];

sub make_repeat_fasta {
   my ($src, $n) = @_;
   my $l = length $src;
   my $start = 0;

   $src .= $src;
   while($n > SEQ_WIDTH) {
      $n -= SEQ_WIDTH;
      say substr($src, $start, SEQ_WIDTH);
      $start += SEQ_WIDTH;
      $start -= $l if $start > $l;
   }
   say substr($src, $start, $n);
}

sub make_random_fasta {
   my ($table, $n) = @_;

   my $scanner;
   my $prob_acc;
   for my $i (0 .. $#$table) {
      $prob_acc += IM * $table->[$i]->[1];
      my $char = $table->[$i]->[0];

      if($i == 0) {
         $scanner .= "if(\$LAST < $prob_acc) { \$str .= '$char'; }\n";
      } elsif($i == $#$table) {
         $scanner .= "else { \$str .= '$char'; }\n";
      } else {
         $scanner .= "elsif(\$LAST < $prob_acc) { \$str .= '$char'; }\n";
      }
   }

   my $code = '
while($n >= SEQ_WIDTH) {
   my $str;
   for (1 .. SEQ_WIDTH) {
      $LAST = ($LAST * IA + IC) % IM;
      ' . $scanner . '
   }
   $n -= SEQ_WIDTH;
   say $str;
}
if($n > 0) {
   my $str;
   for (1 .. $n) {
      $LAST = ($LAST * IA + IC) % IM;
      ' . $scanner . '
   }
   say $str;
}
';
   eval $code;
}



my $n = $ARGV[0] || 27;

say ">ONE Homo sapiens alu";
make_repeat_fasta($alu, $n*2);

say ">TWO IUB ambiguity codes";
make_random_fasta($iub, $n*3);

say ">THREE Homo sapiens frequency";
make_random_fasta($homosapiens, $n*5);
