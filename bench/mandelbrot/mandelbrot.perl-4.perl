# The Computer Language Benchmarks Game
# http://shootout.alioth.debian.org/
#
# contributed by Rodrigo de Almeida Siqueira
# parallelised by Philip Boulain

use strict;
use threads;

my $threads = 6; # Workers; ideally slightly overshoots number of processors

my $MAXITER = 50;
my $LIMIT = 4;
my $xmin = -1.5;
my $ymin = -1;

my ($w, $h);

$w = $h = shift || 200;
my $invN = 2/$w;

print "P4\n$w $h\n"; # PBM image header

sub lines {

  my $y1 = shift;
  my $y2 = shift;
  my @v;

  my $checknext=1;

  for my $y ($y1..$y2) {

    my $Ci = $y * $invN + $ymin;

    X:
    for my $x (0..$w-1) {

      my ($Zr, $Zi, $Tr, $Ti);

      my $Cr = $x * $invN + $xmin;

      if ($checknext) {

        # Iterate with checking (likely outer pixel)
        for (1..$MAXITER) {
          $Zi = 2 * $Zr * $Zi + $Ci;
          $Zr = $Tr - $Ti + $Cr;
          $Ti = $Zi * $Zi;
          $Tr = $Zr * $Zr;

          if ($Tr + $Ti > $LIMIT) {
            push @v, 0; # White pixel
            next X;
          }
        }

        push @v, 1;     # Black pixel
        $checknext = 0; # Is inner pixel.

      } else {

        # Iterate without checking (likely inner pixel)

        for (1..$MAXITER) {
          $Zi = 2 * $Zr * $Zi + $Ci;
          $Zr = $Tr - $Ti + $Cr;
          $Ti = $Zi * $Zi;
          $Tr = $Zr * $Zr;
        }

        if ($Tr+$Ti <= 4) {
          push @v, 1;
        } else { # $Tr+$Ti is a large number or overflow ('nan' or 'inf')
          push @v, 0;
          $checknext = 1;
        }

      }

    }

  }
  return pack 'B*', pack 'C*', @v;

}


use constant MAXPIXEL => 524288; # Maximum pixel buffer per thread
my $each = int($h / $threads);
$each = int(MAXPIXEL / $w) if ($each * $w) > MAXPIXEL;
$each = 1 if $each < 1;
$|=1;

my $y = 0;
my @workers;
# Work as long as we have lines to spawn for or threads to collect from
while(@workers or ($y < $h)) {
  # Create workers up to requirement
  while((@workers < $threads) and ($y < $h)) {
    my $y2 = $y + $each;
    $y2 = $h if $y2 > $h;
    push(@workers, threads->create('lines', $y, $y2-1));
    $y = $y2;
  }
  # Block for result from the leading thread (to keep output in order)
  my $next = shift @workers;
  print $next->join();
}

