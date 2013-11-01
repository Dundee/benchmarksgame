// The Computer Language Benchmarks Game
// http://benchmarksgame.alioth.debian.org/

// contributed by Jos Hirth
// based on the Java/C# versions by Robert F. Tobler, Enotus, Isaac Gouy, and Alp Toker

import 'dart:io';
import 'dart:typed_data';
import 'dart:math' as math;

const int LINE_LENGTH = 60;
const int OUT_BUFFER_SIZE = 256 * 1024;
const int LOOKUP_SIZE = 4 * 1024;
const double LOOKUP_SCALE = (LOOKUP_SIZE - 1) / 1;
const int NEW_LINE_CHARACTER = 10;

/// Returns the code unit of the first char.
int byte (String s) => s.codeUnitAt(0);

/// Returns all code units as an Uint8List.
Uint8List bytes (String s) => new Uint8List.fromList(s.codeUnits);

class Freq {
  int c;
  double p;
  Freq(this.c, this.p);
}

class Random {
  static const int IM = 139968;
  static const int IA = 3877;
  static const int IC = 29573;
  static const double SCALE = LOOKUP_SCALE / IM;
  static int last = 42;

  static double next () {
    return SCALE * (last = (last * IA + IC) % IM);
  }
}

class Out {
  static Uint8List buf = new Uint8List(OUT_BUFFER_SIZE);
  static const int LIMIT = OUT_BUFFER_SIZE - 2 * LINE_LENGTH - 1;
  static int ct = 0;

  static void checkFlush () {
    if (ct >= LIMIT) {
      stdout.add(new Uint8List.view(buf.buffer, 0, ct));
      buf = new Uint8List(OUT_BUFFER_SIZE);
      ct = 0;
    }
  }

  static void finalFlush() {
    stdout.add(new Uint8List.view(buf.buffer, 0, ct));
  }
}

class RandomFasta {
  static final List<Freq> lookup = new List<Freq>(LOOKUP_SIZE);

  static void makeLookup (List<Freq> a) {
    for (int i = 0, j = 0; i < LOOKUP_SIZE; i++) {
      while (a[j].p < i) {
        j++;
      }
      lookup[i] = a[j];
    }
  }

  static void addLine (int bytes) {
    Out.checkFlush();
    int lct = Out.ct;
    while (lct < Out.ct + bytes) {
      double r = Random.next();
      int ai = r.toInt();
      while (lookup[ai].p < r) {
        ai++;
      }
      Out.buf[lct++] = lookup[ai].c;
    }
    Out.buf[lct++] = NEW_LINE_CHARACTER;
    Out.ct = lct;
  }

  static void make(String desc, List<Freq> a, int n) {
    makeLookup(a);

    Out.buf.setRange(Out.ct, Out.ct + desc.length, bytes(desc), 0);
    Out.ct += desc.length;

    while (n > 0) {
      int bytes = math.min(LINE_LENGTH, n);
      addLine(bytes);
      n -= bytes;
    }
  }
}

class RepeatFasta {
  static void make(String desc, Uint8List alu, int n) {
    Out.buf.setRange(Out.ct, Out.ct + desc.length, bytes(desc), 0);
    Out.ct += desc.length;

    Uint8List buf = new Uint8List(alu.length + LINE_LENGTH);
    for (int i = 0; i < buf.length; i += alu.length) {
      buf.setRange(i, i + math.min(alu.length, buf.length - i), alu, 0);
    }

    int pos = 0;
    while (n > 0) {
      int bytes = math.min(LINE_LENGTH, n);
      Out.checkFlush();

      Out.buf.setRange(Out.ct, Out.ct + bytes, buf, pos);
      Out.ct += bytes;

      Out.buf[Out.ct++] = NEW_LINE_CHARACTER;
      pos = (pos + bytes) % alu.length;
      n -= bytes;
    }
  }
}

void main(args) {
  final String ALU =
    "GGCCGGGCGCGGTGGCTCACGCCTGTAATCCCAGCACTTTGG"
    "GAGGCCGAGGCGGGCGGATCACCTGAGGTCAGGAGTTCGAGA"
    "CCAGCCTGGCCAACATGGTGAAACCCCGTCTCTACTAAAAAT"
    "ACAAAAATTAGCCGGGCGTGGTGGCGCGCGCCTGTAATCCCA"
    "GCTACTCGGGAGGCTGAGGCAGGAGAATCGCTTGAACCCGGG"
    "AGGCGGAGGTTGCAGTGAGCCGAGATCGCGCCACTGCACTCC"
    "AGCCTGGGCGACAGAGCGAGACTCCGTCTCAAAAA";
  final List<Freq> IUB = [
    new Freq(byte('a'), 0.27),
    new Freq(byte('c'), 0.12),
    new Freq(byte('g'), 0.12),
    new Freq(byte('t'), 0.27),
    new Freq(byte('B'), 0.02),
    new Freq(byte('D'), 0.02),
    new Freq(byte('H'), 0.02),
    new Freq(byte('K'), 0.02),
    new Freq(byte('M'), 0.02),
    new Freq(byte('N'), 0.02),
    new Freq(byte('R'), 0.02),
    new Freq(byte('S'), 0.02),
    new Freq(byte('V'), 0.02),
    new Freq(byte('W'), 0.02),
    new Freq(byte('Y'), 0.02)
  ];
  final List<Freq> HomoSapiens = [
    new Freq(byte('a'), 0.3029549426680),
    new Freq(byte('c'), 0.1979883004921),
    new Freq(byte('g'), 0.1975473066391),
    new Freq(byte('t'), 0.3015094502008)
  ];

  void sumAndScale(List<Freq> a) {
    double p = 0.0;
    for (int i = 0; i < a.length; i++) {
      a[i].p = (p += a[i].p) * LOOKUP_SCALE;
    }
    a[a.length - 1].p = LOOKUP_SCALE;
  }

  int n = args.length > 0 ? int.parse(args[0]) : 200;

  sumAndScale(IUB);
  sumAndScale(HomoSapiens);

  RepeatFasta.make(">ONE Homo sapiens alu\n", bytes(ALU), n * 2);
  RandomFasta.make(">TWO IUB ambiguity codes\n", IUB, n * 3);
  RandomFasta.make(">THREE Homo sapiens frequency\n", HomoSapiens, n * 5);
  Out.finalFlush();
}
