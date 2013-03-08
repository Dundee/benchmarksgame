/* The Computer Language Benchmarks Game
   http://benchmarksgame.alioth.debian.org/

   contributed by Thomas Sahlin
*/

import 'dart:io';

void main() {
  var src   = "CGATMKRYVBHD";
  var dst   = "GCTAKMYRBVDH";
  var map   = new Map<int, int>();
  var seq   = new List<int>();
  
  // Set up translation map
  
  for (int i = 0; i < src.length; i++)
    map[src.codeUnitAt(i)] = dst.codeUnitAt(i);
  
  src = src.toLowerCase();
  
  for (int i = 0; i < src.length; i++)
    map[src.codeUnitAt(i)] = dst.codeUnitAt(i);
  
  stdin
    .transform(new StringDecoder())
    .transform(new LineTransformer())
    .listen((String line) {
      if (line.startsWith(">")) {
        // Comment line - output the previous sequence and the comment
        
        printSeq(seq);
        print(line);

        // Start a new sequence
        
        seq.clear();
      } else {
        var bytes = line.codeUnits;
        
        for (int byte in bytes) {
          if (map.containsKey(byte))
            seq.add(map[byte]);
          else if (byte != 10)
            seq.add(byte);
        }
      }
    }, onDone: () { printSeq(seq); });
}

void printSeq(List<int> bytes) {
  // Output the sequence in reverse order

  var line = new List<int>();
  
  for (int i = 0; i < bytes.length; i++) {
    line.add(bytes[bytes.length - i - 1]);
    
    // Wrap lines at 60 characters
    
    if (line.length == 60)
    {
      print(new String.fromCharCodes(line));
      line.clear();
    }
  }
  
  if (line.length > 0)
    print(new String.fromCharCodes(line));
}
