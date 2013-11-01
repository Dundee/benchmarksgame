/* The Computer Language Benchmarks game
   http://benchmarksgame.alioth.debian.org/

   contributed by Jos Hirth,
   transliterated from Mario Pernici's Python program
   (which was transliterated from Mike Pall's Lua program)
*/

void main(args){
  int i = 0, k = 0, ns = 0,
      k1 = 1,
      n = 1, a = 0, d = 1, t = 0, u = 0,
      N;

  N = args.length > 0 ? int.parse(args[0]) : 100;

  do{
    k += 1;
    t = n<<1;
    n *= k;
    a += t;
    k1 += 2;
    a *= k1;
    d *= k1;
    if (a >= n){
      int q = n * 3 + a;
      t = q ~/ d;
      u = q % d;
      u += n;
      if (d > u){
        ns = ns * 10 + t;
        i += 1;
        if (i % 10 == 0){
          print('${pad(ns)}\t:$i');
          ns = 0;
        }
        if (i >= N){
          break;
        }
        a -= d * t;
        a *= 10;
        n *= 10;
      }
    }
  }while(true);
}
// Pads a number with up to 9 leading zeros.
pad(int v){
  var s = '000000000$v';
  return s.substring(s.length - 10, s.length);
}
