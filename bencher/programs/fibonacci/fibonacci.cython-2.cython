import sys

cdef double fib(int n):
    cdef double a, b, tmp
    a = 0
    b = 1
    cdef int i
    for i in range(n):
        tmp = a
        a = b
        b = tmp + b
    return a

print(fib(int(sys.argv[1])))
