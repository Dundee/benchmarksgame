/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Jos Hirth, 
   calculation block borrowed from the C# version which was 
      created by Isaac Gouy, Antti Lankila, The Anh Tran, and Robert F. Tobler
*/

import 'dart:io';
import 'dart:isolate';

void main() {
  int n = ((){
    var args = new Options().arguments;
    return args.length > 0 ? int.parse(args[0]) : 200;
  }());

  var threads = Platform.numberOfProcessors;
  var ports = new List(threads);
  var rowFutures = [];

  for (int i = 0; i < threads; i++) {
    ports[i] = spawnFunction(calculateRow);
  }

  var useThread = 0;
  for (int i = 0; i < n; i++) {
    rowFutures.add(ports[useThread].call({
      'n': n,
      'y': i
    }));
    useThread++;
    useThread %= threads;
  }

  print('P4\n$n $n');

  Futures.wait(rowFutures).then((rows) {
    for(var row in rows) {
      stdout.write(row);
    }
  });
}

void calculateRow () {
  port.receive((msg, reply) {
    int n = msg['n'];
    int y = msg['y'];

    int lineLen = (n - 1) ~/ 8 + 1;

    var line = new List<int>(lineLen);

    int xbyte = 0, bits = 1;
    double ci = y * 2.0 / n - 1.0;

    for (int x = 0; x < n; x++) {
      double cr = x * 2.0 / n - 1.5;
      if (bits > 0xff) {
        line[xbyte++] = bits;
        bits = 1;
      }
      double zr = cr,
          zi = ci,
          tr = cr * cr,
          ti = ci * ci;
      int i = 49;
      do {
        zi = zr * zi + zr * zi + ci;
        zr = tr - ti + cr;
        tr = zr * zr;
        ti = zi * zi;
      } while ((tr + ti <= 4.0) && (--i > 0));
      bits = (bits << 1) | (i == 0 ? 1 : 0);
    } while (bits < 0x100) bits = (bits << 1);
    line[xbyte] = bits;

    reply.send(line);
  });
}
