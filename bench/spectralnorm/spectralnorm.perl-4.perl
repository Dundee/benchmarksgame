# The Computer Language Benchmarks Game
# http://shootout.alioth.debian.org/
#
# Original Perl contributed by Andrew Rodland
# Original JavaScriptV8 contributed by Ian Osgood
# Modified by Alexey Tverskov

use strict;
#use warnings;


sub A {
  use integer;
  my $div = ($_[0] + $_[1]) * ($_[0]+$_[1]+1) / 2 + $_[0] + 1;
  no integer;
  return 1 / $div; 
}

sub Au {
  my @u = @{$_[0]};
  my @v;
  my $sum;

  for my $i (0 .. $#u) {
    $sum = 0;
    $sum += A($i,$_) * $u[$_]    for 0 .. $#u;
    $v[$i] = $sum;
  }

  return \ @v;
}

sub Atu {
  my @u = @{$_[0]};
  my @v;

  my $sum;
  for my $i (0 .. $#u) {
    $sum = 0;
    $sum += A($_,$i) * $u[$_]    for 0 .. $#u;
    $v[$i] = $sum;
  };

  return \ @v;
}

sub AtAu {
  return Atu( Au( $_[0]  ) );
}

sub spectralnorm {
  my $n = $_[0];
  my $u = [ (1) x $n ];
  my $v; 
  my $vv = 0;
  my $vBv = 0;


  for (0 .. 9) {
    $v = AtAu( $u );
    $u = AtAu( $v );
  }
  $vBv += $u->[$_] * $v->[$_]    for 0 .. $n-1;
  $vv += $_ ** 2                 foreach @$v;

  return sqrt($vBv/$vv);
}

printf( "%0.9f\n",  spectralnorm($ARGV[0]));
