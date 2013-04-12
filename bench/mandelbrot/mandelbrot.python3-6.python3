# The Computer Language Benchmarks Game
# http://benchmarksgame.alioth.debian.org/

# contributed by Dieter Weber

import numpy as np
import sys
import os
import multiprocessing
import ctypes

# set HALF=True to get twice the speed
HALF = False

# We knowingly produce overflows, therefore
# ignore warning
np.seterr(all='ignore')

size = int(sys.argv[1])
if HALF:
    # we only calculate the upper half, but calculated area is always asymmetric
    # by one line, so always one more line necessary
    calcsize = size // 2 + 1
else:
    calcsize = size
# boundaries of the calculated area
remin = -1.5
remax = 0.5
# if we break the symmetry here, we have to fix the "calculate half" aspect
# of the code!
imin = -1.
imax = -imin

# there are apparently a few differences in handling strings and byte arrays...
V3 = sys.version_info >= (3, )

# maximum number of points calculated in parallel to avoid
# excessive memory use
# this seems to be the optimum on my machine, fairly low value imho
maxchunk = 1<<14

# fixed to match reference output
# iterations = 49

# List of iteration steps, supplied by hand as number of iterations is fixed
# optimized for speed, ignoring overflows
# this is apparently a good tradeoff between the cost of making a new array
# and the cost of calculating more points
iteration_steps = [9] + [10]*4

# distance between points in real direction
restep = (remax - remin)/size
# distance between points in imaginary direction
imstep = (imax - imin)/size

# number of bits
# remainder of experiments with long long int for bit shifting,
# but no advantage, therefore byte boundary of lines not implemented for
# other types than unsigned byte
(bits, calctype) = (8, 'u1')
bytesize = 8

# precalculate real part of one line  and reuse later
reline = np.linspace(remin, remax - restep, size)
# precalculate imaginary parts and reuse later
if HALF:
    imline = 1j*np.linspace(imin, 0 - imstep*(size%2)*0.5, calcsize)
else:
    imline = 1j*np.linspace(imin, imax - imstep, calcsize)

# padding to start new line at byte boundary following pbm format
# size in [1,8] -> 1 byte per line ; size in [9,16]-> 2 bytes per line etc.
linesize = (size - 1)//bytesize + 1

# PBM header, important to calculate buffer size
header = "P4\n{0} {0}\n".format(size)
if V3:
    header = bytes(header, 'ascii')
header_offset = len(header)

bufferlen = len(header) + linesize * size

# creates ctypes array in shared memory
# REMARK: mmap.mmap can do almost the same here, but by the benchmark's specs
# we have to write to stdout anyway.
# if there are memory size issues, we can
# mmap a temporary file here and also adjust maxchunk above,
# but 16000x16000 only uses approx. 32 MB, no problem on todays machines
# if we wanted bigger than about 50000x50000, or minimize memory usage, it could
# however be a good idea to implement the mmap version.
sharedbitmap = multiprocessing.Array(ctypes.c_char, bufferlen, lock=False)
sharedbitmap[0:len(header)] = header

# not more to avoid task switching overhead and surplus memory usage
# one process already puts one core to 100%, no waiting, locking etc. :-)
# may not be portable, but no worries on standard Linux
workers = multiprocessing.cpu_count()

# calculate line package size, either divide fairly per cpu or limit
# by memory use
maxlines = maxchunk // size
# make sure we calculate at least one line per package

# we only calculate the upper half of the set and exploit it's symmetry!
lines_per_chunk = max(1, min(calcsize//workers,  maxlines))

# number of tasks, only upper half of the set!
packages = max(1, calcsize // lines_per_chunk)
# hehe, we could have many processors...
if workers <= calcsize:
# make sure it's dividable by number of processors to have all working
# There's a small imbalance at the very end of the program run because
# different chunks have different calculation time
    packages += packages%workers
else:
    # one line per package
    packages = calcsize
# To make sure we can calculate very small sets on machines with lots of
# processors: max(1, ...)
lines_per_chunk = max(1, calcsize // packages)
    
tasks = []
for i in range(packages):
    tasks.append((i*lines_per_chunk, lines_per_chunk))

# see what lines are not covered yet, distribute them among the tasks
(last_offset, last_lines) = tasks[-1]
missing_lines = calcsize - (last_offset+last_lines)
for i in range(missing_lines):
    index = -(missing_lines-i)
    (offset, lines) = tasks[index]
    tasks[index] = (offset + i, lines + 1)

# modifies z! call by reference!
# iterate z according to formula, set all elements
# in the set to 0
def mandeliterations(z):
    # calculate subsequently shorter lists and keep track of indices
    indexlist = []
    calcc = z.copy()
    calcz = z
    # filtering the list is expensive,
    # calculate several iterations before filtering
    for iterations in iteration_steps:
        for i in range(iterations):
            calcz **= 2
            calcz += calcc
        indices = np.nonzero(abs(calcz) < 2)
        # I guess that continuous arrays are better for fast iterating,
        # therefore create new arrays
        calcz = calcz[indices]
        calcc = calcc[indices]
        indexlist.append(indices)
    # "collapes" the index list from the bottom to get the elements
    # remaining in the set
    mandelbrot = indexlist.pop()
    # a bit messy because of the index format that numpy uses
    for indices in reversed(indexlist[1:]):
        mandelbrot = indices[0][mandelbrot]
    mandelbrot = (indexlist[0][0][mandelbrot], indexlist[0][1][mandelbrot])
    # finally, set everything in the set = 0
    # maybe the index list could be used to set bitmap directly?
    # But this seems cleaner in terms of code structure: only floats here,
    # keep bitmap business in pbmline()
    z[mandelbrot] = 0

# generate/allocate block of complex numbers for subsequent iteration
def mandelblock(line_offset, lines):
    # maybe numpy.mgrid or another method would be faster, but not tried yet...
    (re, im) = np.meshgrid(reline, imline[line_offset:line_offset+lines])
    # reuse memory
    im += re
    return im

# convert data array into "compressed" bitmap to be written to binary pbm file
def pbmline(points, lines):
    # each point is in [0, 1] now, 8 bit unsigned int
    bitmap = np.zeros((lines, linesize*bits), dtype=calctype)
    # respect  the "padding" bits at the end of
    # the line to get byte boundaries
    bitmap[:,:size] = points==0
    # make blocks with 8 bits
    bitmap = bitmap.reshape((linesize*lines, bits))
    # shift bits, accumulate in highest bit
    for bit in range(0,bits-1):
        # fix bit order here
        bitmap[:,bit] <<= (bits-bit-1)
        bitmap[:,bits-1] += bitmap[:,bit]
    # return accumulator
    result = bitmap[:,bits-1]
    return result

if V3:
    def tobytes(a):
        return bytes(iter(a))
else:
    def tobytes(a):
        return a.tostring()

if HALF:
    # move bitmap fragments to output
    # eventually modifies bitmap in the process! 
    # But we're done after :-)
    def copybits(line_offset, bitmap, lines):
        # make sure that array is "flat"
        bitmap.reshape(-1)
        # use this for number of bytes, reshaping influences len(bitmap)
        len_bitmap = len(bitmap)
        startbyte = header_offset + line_offset * linesize
        # program works with 8/8 now, less headache
        # but keep this for explicity
        copybytes = len_bitmap*bits//bytesize
        # didn't get ctypes.memcopy to work,
        # this does it although it may be a bit slower
        # bitmap HAS to be flat
        sharedbitmap[startbyte:startbyte+len_bitmap] = tobytes(bitmap)
        # now reshape to lines x lines in order to reverse and exploit symmetry
        bitmap = bitmap.reshape((lines, linesize))
        # reverse the lines
        bitmap = bitmap[::-1, :]
        startbyte = bufferlen - line_offset * linesize - len_bitmap + linesize
        stopbyte = startbyte + len_bitmap
        # the calculated area is not symmetric, we have overlap
        # therefore clip overhang
        if startbyte < (bufferlen - header_offset) // 2:
            bitmap = bitmap[1:,:]
            startbyte += linesize
        if stopbyte > bufferlen:
            bitmap = bitmap[:-1,:]
            stopbyte -= linesize
        # flat again for output
        bitmap = bitmap.reshape(-1)
        sharedbitmap[startbyte:startbyte+len_bitmap] = tobytes(bitmap)
else:
    # move bitmap fragments to output
    # eventually modifies bitmap in the process! 
    # But we're done after :-)
    def copybits(line_offset, bitmap, lines):
        # make sure that array is "flat"
        bitmap.reshape(-1)
        # use this for number of bytes, reshaping influences len(bitmap)
        len_bitmap = len(bitmap)
        startbyte = header_offset + line_offset * linesize
        # program works with 8/8 now, less headache
        # but keep this for explicity
        copybytes = len_bitmap*bits//bytesize
        # didn't get ctypes.memcopy to work,
        # this does it although it may be a bit slower
        # bitmap HAS to be flat
        sharedbitmap[startbyte:startbyte+len_bitmap] = tobytes(bitmap)

# function to be called with element of the task array,
# suitable for mapping (no output, only modifies shared buffer)
def work(tup):
    (line_offset, lines) = tup
    block = mandelblock(line_offset, lines)
    mandelbrot = mandeliterations(block)
    bitmap = pbmline(block, lines)
    copybits(line_offset, bitmap, lines)

pool = multiprocessing.Pool(workers)
# for debugging: just use map(...) instead of pool.map(...)
# to get the exceptions + trace
# global variables etc. are shared between processes! :-)
pool.map(work, tasks)
#for tup in tasks:
    #work(tup)



# dump it!
if V3:
    sys.stdout.buffer.write(sharedbitmap)
else:
    sys.stdout.write(sharedbitmap.raw)
