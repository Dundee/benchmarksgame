/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Jos Hirth, 
   calculation block borrowed from the C# version which was 
      created by Isaac Gouy, Antti Lankila, The Anh Tran, and Robert F. Tobler
*/

import 'dart:io';
import 'dart:isolate';
import 'dart:async';
import 'dart:typed_data';

void main() {
  int n = ((){
    var args = new Options().arguments;
    return args.length > 0 ? int.parse(args[0]) : 200;
  }());

  var threads = Platform.numberOfProcessors;
  var ports = new List(threads);
  var segmentFutures = new List(threads);
  int lineLen = (n - 1) ~/ 8 + 1;
  var lines = new List<Uint8List>(n);

  var segmentSize = new List.filled(threads, n ~/ threads);
  segmentSize[0] += n % threads;

  var from = 0;
  for (int i = 0; i < threads; i++) {
    var len = segmentSize[i];
    ports[i] = spawnFunction(calculateSegment);
    segmentFutures[i] = ports[i].call({
      'n': n,
      'from': from,
      'len': len
    });
    from += len;
  }

  stdout.write('P4\n$n $n\n');

  Future.wait(segmentFutures).then((segments) {
    for (var segment in segments) {
      for (var line in segment) {
        stdout.add(line);
      }
    }
  });
}

Uint8List calculateLine (int n, int y) {
  int lineLen = (n - 1) ~/ 8 + 1;

  var line = new Uint8List(lineLen);

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

  return line;
}

void calculateSegment () {
  port.receive((msg, replyTo) {
    int n = msg['n'];
    int from = msg['from'];
    int len = msg['len'];

    var lines = new List<Uint8List>(len);
    for (int i = 0; i < len; i++) {
      lines[i] = calculateLine(n, from + i);
    }

    replyTo.send(lines);
  });
}
