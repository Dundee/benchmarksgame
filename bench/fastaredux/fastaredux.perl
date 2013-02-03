# The Computer Language Benchmarks game
# http://benchmarksgame.alioth.debian.org/
#
# contributed by David Pyke
# tweaked by Danny Sauer
# optimized by Steffen Mueller
# tweaked by Kuang-che Wu
# adapted for fasta-redux by Tugrul Galatali

use strict;
use warnings;
use constant IM => 139968;
use constant IA => 3877;
use constant IC => 29573;

use constant LINELENGTH => 60;

use constant LUTSIZE => 4096;

my $LAST = 42;
sub gen_random {
    return map {( ($_[0] * ($LAST = ($LAST * IA + IC) % IM)) / IM )} 1..($_[1]||1);
}

sub makeCumulative {
    my $genelist = shift;
    my $cp = 0.0;

    $_->[1] = $cp += $_->[1] foreach @$genelist;

    # Guarantee 100%
    $genelist->[-1]->[1] = 1;
}

sub selectRandom {
    my $genelist = shift;
    my $lut = shift;
    my $number = shift || 1;
    my @r = gen_random(1, $number);

    my $s;
    foreach my $r (@r) {
        my $ii = $lut->[int($r * LUTSIZE)];
        unless (ref($ii)) {
            $s .= $ii;
        } else {
            my $index = $$ii;
            while ($r >= $genelist->[$index]->[1]) {
                ++$index;
            }
            $s .= $genelist->[$index]->[0];
        }
    }

    return $s;
}


sub makeRandomFasta {
    my ($id, $desc, $n, $genelist) = @_;

    print ">", $id, " ", $desc, "\n";
    
    my @lut;
    for (my $ii = 0; $ii <= $#{$genelist}; ++$ii) {
        my $lutIndex = int($genelist->[$ii]->[1] * LUTSIZE);

        # Skip growing lookup on collision
        if ($#lut < $lutIndex) {
          # Put nucleotide in lookup table if unambiguous
          push @lut, ( $genelist->[$ii]->[0] ) x ($lutIndex - $#lut - 1);

          # Otherwise put a reference to the starting index of a linear search
          my $index = $ii;
          push @lut, \$index;
        }
    }

    # print whole lines
    foreach (1 .. int($n / LINELENGTH) ){
        print selectRandom($genelist, \@lut, LINELENGTH), "\n";
    }
    # print remaining line (if required)
    if ($n % LINELENGTH){
        print selectRandom($genelist, \@lut, $n % LINELENGTH), "\n";
    }
}

sub makeRepeatFasta {
    my ($id, $desc, $s, $n) = @_;

    print ">", $id, " ", $desc, "\n";

    my $r = length $s;
    my $ss = $s . $s . substr($s, 0, $n % $r);
    for my $j(0..int($n / LINELENGTH)-1) {
	my $i = $j*LINELENGTH % $r;
	print substr($ss, $i, LINELENGTH), "\n";
    }
    if ($n % LINELENGTH) {
	print substr($ss, -($n % LINELENGTH)), "\n";
    }
}


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

my $alu =
    'GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG' .
    'GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA' .
    'CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT' .
    'ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA' .
    'GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG' .
    'AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC' .
    'AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA';

######################################################################
#main

my $n = ($ARGV[0] || 1000) ;

makeCumulative($iub);
makeCumulative($homosapiens);

makeRepeatFasta ('ONE', 'Homo sapiens alu', $alu, $n*2);
makeRandomFasta ('TWO', 'IUB ambiguity codes', $n*3, $iub);
makeRandomFasta ('THREE', 'Homo sapiens frequency', $n*5, $homosapiens);
