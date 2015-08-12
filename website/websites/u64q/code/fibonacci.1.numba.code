import numpy
import sys
from numba.decorators import autojit


@autojit
def fib(n):
    return (numpy.matrix('1 1; 1 0', numpy.dtype('object')) ** n).tolist()[0][1]

print(fib(int(sys.argv[1])))
