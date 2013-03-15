/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   Contributed by Thomas Sahlin
   Lookup table optimization by Alex Tatumizer
*/

import 'dart:io';

void main() {
  var src   = "CGATMKRYVBHD";
  var dst   = "GCTAKMYRBVDH";
  var tbl   = new List<int>(256);
  var seq   = new List<int>();
  
  // Set up lookup table
  
  for (int i = 0; i < tbl.length; i++)
    tbl[i] = i;
  
  for (int i = 0; i < src.length; i++) {
    tbl[src.codeUnitAt(i)]                = dst.codeUnitAt(i);
    tbl[src.toLowerCase().codeUnitAt(i)]  = dst.codeUnitAt(i);
  }
  
  // Function to print the sequences in reverse order
  
  void printSeq() {
    for (int i = seq.length - 60; i >= 0; i -= 60) {
      var line = seq.getRange(i, 60);
      
      print(new String.fromCharCodes(line.reversed.toList()));
    }

    if (seq.length % 60 > 0) {
      var line = seq.getRange(0, seq.length % 60);
      
      print(new String.fromCharCodes(line.reversed.toList()));
    }
  }
  
  // Start processing
  
  stdin
    .transform(new StringDecoder())
    .transform(new LineTransformer())
    .listen((String line) {
      if (line.startsWith(">")) {
        // Comment line - output the previous sequence and the comment
        
        printSeq();
        print(line);

        // Start a new sequence
        
        seq.clear();
      } else {
        // Translate characters and add them to the sequence
        
        for (int byte in line.codeUnits)
          seq.add(tbl[byte]);
      }
    }, onDone: () { printSeq(); });
}
