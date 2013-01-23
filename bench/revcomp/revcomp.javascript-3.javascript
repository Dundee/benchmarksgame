/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Jos Hirth
   modified by 10iii
*/

var line, out, reverseFormat, complement;

complement = {
   y: 'R',
   v: 'B',
   w: 'W',
   t: 'A',
   u: 'A',
   r: 'Y',
   s: 'S',
   n: 'N',
   m: 'K',
   k: 'M',
   h: 'D',
   g: 'C',
   d: 'H',
   b: 'V',
   c: 'G',
   a: 'T',
   Y: 'R',
   V: 'B',
   W: 'W',
   T: 'A',
   U: 'A',
   R: 'Y',
   S: 'S',
   N: 'N',
   M: 'K',
   K: 'M',
   H: 'D',
   G: 'C',
   D: 'H',
   B: 'V',
   C: 'G',
   A: 'T'
};

reverseFormat = function (a, complement) {
   var i, j, l, line, c = 1;
   var printbuff = [];
   var outbuff = new Array(61);
   outbuff[0] = '';
   for (l = a.length; l--;) {
      line = a[l];
      for (i = line.length; i--; c++) {
         outbuff[c] = complement[line[i]];
         if (c === 60) {
            printbuff.push(outbuff.join(''));
            c = 0;
         }
      }
      if((l%1000) === 0){
         print(printbuff.join('\n'));
         printbuff = [];
      }
   }
   if (c > 1) {
      for (j = c; j < 61; j++){
         outbuff[j] = '';
      }
      printbuff.push(outbuff.join(''));
   }
   if (printbuff.length > 0){
      print(printbuff.join('\n'));
      printbuff = [];
   }
};

out = [];
while ((line = readline())) {
   if (line[0] !== '>') {
      out.push(line);
   } else {
      reverseFormat(out, complement);
      out = [];
      print(line);
   }
}

reverseFormat(out, complement);
