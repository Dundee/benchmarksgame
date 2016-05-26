import os, time

cdef measure(func, int num1, int num2):
    t0 = time.time()
    func(num1, num2)
    print(
        func, 
        "%d bytes, %.2fus per write" % (
            num2, 
            (time.time() - t0) / num1 * 1000 * 1000
        )
    )

cdef fwrite(int num, int num2):
    fd = os.open("/dev/null", os.O_WRONLY)
    for i in range(num):
        os.write(fd, b" " * num2)

cdef fread(int num, int num2):
    fd = os.open("/dev/full", os.O_RDONLY)
    for i in range(num):
        os.read(fd, num2)

cdef file_write(int num, int num2):
    f = open("/dev/null", "w")
    for i in range(num):
        f.write(" " * num2)
    f.flush()

cdef file_read(int num, int num2):
    f = open("/dev/full")
    for i in range(num):
        f.read(num2)

if __name__ == '__main__':
    measure(fread, 10000000, 100)
    measure(fwrite, 10000000, 100)
    measure(fread, 10000000, 1000)
    measure(fwrite, 10000000, 1000)
    measure(fread, 1000000, 10000)
    measure(fwrite, 1000000, 10000)
    measure(file_read, 10000000, 100)
    measure(file_write, 10000000, 100)
    measure(file_read, 10000000, 1000)
    measure(file_write, 10000000, 1000)
    measure(file_read, 1000000, 10000)
    measure(file_write, 1000000, 10000)
