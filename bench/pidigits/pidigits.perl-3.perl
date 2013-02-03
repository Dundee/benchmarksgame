# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org/
#
# transliterated from Mario Pernici's Python 3 program
# contributed by Jesse Luehrs

use strict;
use Math::GMP;

my $N = $ARGV[0];

my ($i, $k, $ns) = (0, 0, 0);
my $k1 = 1;
my ($n, $a, $d, $t, $u) = map Math::GMP->new($_), (1, 0, 1, 0, 0);

while (1) {
    $k++;
    $t = $n * 2;
    $n *= $k;
    $a += $t;
    $k1 += 2;
    $a *= $k1;
    $d *= $k1;
    if ($a >= $n) {
        $u = $n * 3 + $a;
        $t = $u / $d;
        $u -= $t * $d;
        $u += $n;
        if ($d > $u) {
            $ns = $ns * 10 + $t;
            $i++;
            if ($i % 10 == 0) {
                printf "%010d\t:%d\n", $ns, $i;
                $ns = 0;
            }
            last if $i >= $N;
            $a -= $d * $t;
            $a *= 10;
            $n *= 10;
        }
    }
}
