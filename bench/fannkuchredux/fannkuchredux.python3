# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org/
#
# contributed by Miroslav Rubanets
# algorithm is based on Java 6 source code by Oleg Mazurov
# source is based on Miroslav Rubanets' C++ submission.
# converted to python by Ian P. Cooke

import sys
from multiprocessing import cpu_count, Pool

MAX_PROBLEM_SIZE = 12
MAX_CPU_LIMIT = 4

class Result:
    def __init__(self, maxflips=0, checksum=0):
        self.maxflips = maxflips
        self.checksum = checksum
    def accumulate(self, other):
        self.maxflips = max( self.maxflips, other.maxflips );
        self.checksum += other.checksum;
        
class PermutationGenerator:
    def __init__(self, length, factorials, idx):
        self.perm = [0] * MAX_PROBLEM_SIZE
        self.count = [0] * MAX_PROBLEM_SIZE
        self.length = length
        self.factorials = factorials
        self.first_permutation( idx )

    def first_permutation( self, idx ):
        pp = [0] * MAX_PROBLEM_SIZE
        p =  list(range(MAX_PROBLEM_SIZE))
        
        for i in range( self.length - 1, 0, -1 ):
            d = idx // self.factorials[i]
            self.count[i] = d
            idx = idx % self.factorials[i]
            pp[:i+1] = p[:i+1]
            for j in range(0, i+1):
                if j+d <= i:
                    p[j] = pp[j+d]
                else:
                    p[j] = pp[j+d-i-1]
        self.perm[:self.length] = p[:self.length]

    def next_permutation( self ):
        #rotate
        first = self.perm[0]
        self.perm[0] = self.perm[1]
        self.perm[1] = first
        self.count[1] += 1

        i=1
        while self.count[i] > i:
            self.count[i] = 0
            i += 1
            if i >= MAX_PROBLEM_SIZE:
              break
            self.count[i] += 1
            #rotate
            first = self.perm[0]
            self.perm[:i] = self.perm[1:i+1]
            self.perm[i] = first

def task_body( length, factorials, first_index, last_index ):
    try:
      g = PermutationGenerator(length, factorials, first_index)
      maxflips = 0
      checksum = 0
      for i in range(first_index, last_index):
          data = [0] * MAX_PROBLEM_SIZE
          flips = 0;        
          f =  g.perm[0];
          if f > 0:
              data[:length] = g.perm[:length]
              while True:
                  data[:f+1] = data[f::-1] #reverse
                  flips += 1
                  f = data[0]
                  if f == 0:
                      break
          maxflips = max(maxflips, flips)
          checksum +=  flips if i%2 == 0 else -flips
          g.next_permutation()
    except:
      import traceback; traceback.print_exc()

    return Result(maxflips=maxflips, checksum=checksum)


def create_factorials(length):
    factorials = [0] * (MAX_PROBLEM_SIZE+1)
    factorials[0] = 1
    for i in range(1, length+1):
        factorials[i] = factorials[i-1]*i;
    return factorials

usage = """usage fannkuchredux number
number has to be in range [3-12]\n""";

def main():
    if len(sys.argv) < 2:
        print(usage)
        return 1

    length = int(sys.argv[1])
    if length < 3 or length > MAX_PROBLEM_SIZE:
        print(usage)
        return 2

    n = min( cpu_count(), MAX_CPU_LIMIT )
    processors = Pool(processes=n)

    factorials = create_factorials(length)

    result = Result()
    index = 0
    index_max = factorials[length]
    index_step = (index_max + n-1) // n
    for i in range(0,n):
        processors.apply_async(task_body, [ length, factorials, index, index + index_step], callback=result.accumulate)
        index += index_step

    processors.close()
    processors.join()
    processors.terminate()
    
    print("%d\nPfannkuchen(%d) = %d" % ( result.checksum, length, result.maxflips) )
    return 0

if __name__ == "__main__":
  main()
