
# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org/
#
# Contributed by Sebastien Loisel
# Fixed by Isaac Gouy
# Sped up by Josh Goldfoot
# Dirtily sped up by Simon Descarpentries
# Used list comprehension by Vadim Zelenin
# 2to3
# Sped up with numpy by @tim_1729

from math      import sqrt
from sys       import argv
import numpy


def eval_A(i, j):
    ij = i+j
    return 1.0 / (ij * (ij + 1) / 2 + i + 1)


def eval_A_times_u(u):
    local_eval_A = eval_A

    n = u.shape[0]
    # output is n items
    iis = numpy.arange(n)
    iis = numpy.reshape(iis,(n,1))
    j = numpy.arange(n)
    j = numpy.tile(j,(n,1)) # j is a matrix. Every row is [ 0, 1, 2, ...]
    u_j = numpy.tile(u,(n,1))
    output = numpy.sum(local_eval_A(iis,j)*u_j,axis=1)
    return output


def eval_At_times_u(u):
    local_eval_A = eval_A

    n = u.shape[0]
    # output is n items
    # each item is sum of things in loop
    iis = numpy.arange(n)
    iis = numpy.reshape(iis,(n,1))
    j = numpy.arange(n)
    j = numpy.tile(j,(n,1))
    u_j = numpy.tile(u,(n,1))
    output = numpy.sum(local_eval_A(j,iis)*u_j,axis=1)
    return output



def eval_AtA_times_u(u):
    return eval_At_times_u(eval_A_times_u(u))


def main():
    n = int(argv[1])
    u = numpy.ones(n)
    local_eval_AtA_times_u = eval_AtA_times_u

    for dummy in range(10):
        v = local_eval_AtA_times_u(u)
        u = local_eval_AtA_times_u(v)

    vBv = numpy.sum( u * v )
    vv = numpy.sum( v * v )

    print("%0.9f" % (numpy.sqrt(vBv/vv)))

if __name__ == "__main__":
    main()
