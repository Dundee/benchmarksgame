# The Computer Language Benchmarks game
# http://benchmarksgame.alioth.debian.org/
#
# contributed by David Pyke
# tweaked by Danny Sauer
# optimized by Steffen Mueller
# tweaked by Kuang-che Wu
# optimized by Rodrigo de Oliveira

use strict;
use warnings;
use constant IM => 139968;
use constant IA => 3877;
use constant IC => 29573;

use constant LINELENGTH => 60;

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

my @randomSeq;
my $INITIAL_STATE = 42;
my $LAST = 0;

sub makeRandomSeq {
    my $ia = IA;
    my $ic = IC;
    my $s = $INITIAL_STATE;
    while( 1 ) {
        $s = ($s * $ia + $ic) % IM;
        push @randomSeq, $s;
        last if $s == $INITIAL_STATE;
    }
}

sub makeCumulative {
    my $genelist = shift;
    my $cp = 0.0;

    $_->[1] = $cp += $_->[1] foreach @$genelist;
}

sub makeRandomFasta {
    my ($id, $desc, $n, $genelist) = @_;

    print ">", $id, " ", $desc, "\n";

    my @chars = map { $_->[0] } @$genelist;
    my @probs = map { $_->[1] } @$genelist;
    my $im = IM;
    my @tab;
    for my $i ( 0 .. IM-1 ) {
        my $div = 0.0  + $i / $im;
        my $ix = scalar grep { $_ < $div } @probs;
        push @tab, $chars[ $ix ];   
    }
    my $lut = join '', @tab[ @randomSeq[ 0..(IM-1) ] ];
    $lut .= substr( $lut, 0, LINELENGTH ); 

    # print whole lines
    my $pos = $LAST;
    for ( 1 .. int($n / LINELENGTH) ){
        print substr( $lut, $pos, LINELENGTH ) . "\n";
        $pos = ( $pos + LINELENGTH ) % IM;
    }
    $LAST = $pos;
    # print remaining line (if required)
    if ($n % LINELENGTH) {
        my $kpos = ( $pos + ( $n % LINELENGTH ) ) % IM;
        my $s = $pos < $kpos
            ? substr( $lut, $pos, $kpos - $pos )
            : substr($lut, $pos ) . substr($lut, 0, $kpos - $pos) ;
        print "$s\n";
        $LAST = $kpos;
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

######################################################################
#main

my $n = $ARGV[0] || 1000;

makeRandomSeq();

makeCumulative($iub);
makeCumulative($homosapiens);

makeRepeatFasta ('ONE', 'Homo sapiens alu', $alu, $n*2);
makeRandomFasta ('TWO', 'IUB ambiguity codes', $n*3, $iub);
makeRandomFasta ('THREE', 'Homo sapiens frequency', $n*5, $homosapiens);
