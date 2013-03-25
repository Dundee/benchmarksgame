/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Contributed by Jesse Millikan
   Modified by Matt Baker
*/

'use strict';

function frequency(seq, length){
  var freq = {},
      n = seq.length - length + 1,
      sub, i;

  for(i = 0; i < n; i++){
    sub = seq.substr(i, length);
    freq[sub] = (freq[sub] || 0) + 1;
  }

  return freq;
}


function sort(seq, length){
  var f = frequency(seq, length),
      keys = Object.keys(f), 
      n = seq.length - length + 1,
      i;

  keys.sort(function(a, b){ return f[b] - f[a]; });

  for(i in keys) {
    print(keys[i], (f[keys[i]] * 100 / n).toFixed(3));
  }
  
  print();
}


function find(seq, s){
  var f = frequency(seq, s.length);
  print((f[s] || 0) + "\t" + s);
}


function readSequence() {
  var lines = [],
      l;

  while(readline().substr(0, 6) !== '>THREE'); // no body

  while((l = readline()) && l[0] !== '>') {
    lines.push(l);
  }

  return lines.join('').toUpperCase();
}


var seq = readSequence();

sort(seq, 1);
sort(seq, 2);

find(seq, "GGT");
find(seq, "GGTA");
find(seq, "GGTATT");
find(seq, "GGTATTTTAATT");
find(seq, "GGTATTTTAATTTATAGT");