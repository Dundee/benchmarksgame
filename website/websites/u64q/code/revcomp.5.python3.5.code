# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org/

# contributed by Jacob Lee, Steven Bethard, et al
# 2to3, fixed by Daniele Varrazzo
# modified by Daniel Nanz
# read() input, write in reverse, cleanups by J. Kleint

import sys

def show(seq, table, write=sys.stdout.buffer.write, nl=b'\n'):
    header, s = seq.split(nl, 1)
    s = s.translate(table, nl)
    write(b'>' + header + nl)
    for i in range(len(s) - 60 - 1, -1, -60):
        write(s[i + 60:i:-1] + nl)
    write(s[0:i + 1][::-1] + nl)

def main():
    table = bytes.maketrans(b'ACBDGHKMNSRUTWVYacbdghkmnsrutwvy', 
                            b'TGVHCDMKNSYAAWBRTGVHCDMKNSYAAWBR')
    for seq in sys.stdin.buffer.read().split(b'>')[1:]:
        show(seq, table)

main()
