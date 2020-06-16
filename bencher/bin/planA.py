"""
measure with libgtop2 and CPU affinity
"""
__author__ = 'Isaac Gouy'


import os
import pickle
import signal
import sys
import threading
import time
from errno import ENOENT
from subprocess import Popen

import gi
gi.require_version('GTop', '2.0')
from gi.repository import GTop

from domain import Record


def set_process_affinity_mask(pid, mask):
    os.sched_setaffinity(pid, mask)


def measure(arg, commandline, delay, maxtime, outFile=None, errFile=None, inFile=None, logger=None, affinitymask=None):

    r, w = os.pipe()
    forkedPid = os.fork()

    if forkedPid:  # read pickled measurements from the pipe
        os.close(w)
        rPipe = os.fdopen(r, "rb")
        r = pickle.Unpickler(rPipe)
        measurements = r.load()
        rPipe.close()
        os.waitpid(forkedPid, 0)
        return measurements

    else:
        # Sample thread will be destroyed when the forked process _exits
        class Sample(threading.Thread):
            def __init__(self, program):
                threading.Thread.__init__(self)
                self.setDaemon(1)
                self.timedout = False
                self.p = program
                self.maxMem = 0
                self.childpids = None
                self.start()

            def run(self):
                try:
                    remaining = maxtime
                    while remaining > 0:
                        proc_mem = GTop.glibtop_proc_mem()
                        GTop.glibtop_get_proc_mem(proc_mem, self.p)
                        mem = proc_mem.resident
                        time.sleep(delay)
                        remaining -= delay
                        # race condition - will child processes have been created yet?
                        self.maxMem = max((mem + self.childmem()) / 1024, self.maxMem)
                    else:
                        self.timedout = True
                        os.kill(self.p, signal.SIGKILL)
                except OSError as xxx_todo_changeme:
                    (e, err) = xxx_todo_changeme.args
                    if logger:
                        logger.error('%s %s', e, err)

            def childmem(self):
                if self.childpids is None:
                    self.childpids = set()
                    proc_list = GTop.glibtop_proclist()
                    for each in GTop.glibtop_get_proclist(proc_list, 0, 0):
                        uid = GTop.glibtop_proc_uid()
                        GTop.glibtop_get_proc_uid(uid, each)
                        if uid.ppid == self.p:
                            self.childpids.add(each)
                mem = 0
                for each in self.childpids:
                    proc_mem = GTop.glibtop_proc_mem()
                    GTop.glibtop_get_proc_mem(proc_mem, each)
                    mem += proc_mem.resident
                return mem

        try:

            def setAffinity():
                if affinitymask:
                    set_process_affinity_mask(os.getpid(), affinitymask)

            m = Record(arg)

            # only write pickles to the pipe
            os.close(r)
            wPipe = os.fdopen(w, 'wb')
            w = pickle.Pickler(wPipe)

            # gtop cpu is since machine boot, so we need a before measurement
            cpu = GTop.glibtop_cpu()
            GTop.glibtop_get_cpu(cpu)
            cpus0 = cpu.xcpu_total
            cpus0_idle = cpu.xcpu_idle
            start = time.time()

            # spawn the program in a separate process
            p = Popen(commandline, stdout=outFile, stderr=errFile, stdin=inFile, preexec_fn=setAffinity)

            # start a thread to sample the program's resident memory use
            t = Sample(program=p.pid)

            # wait for program exit status and resource usage
            rusage = os.wait3(0)

            # gtop cpu is since machine boot, so we need an after measurement
            elapsed = time.time() - start
            GTop.glibtop_get_cpu(cpu)
            cpus1 = cpu.xcpu_total
            cpus1_idle = cpu.xcpu_idle

            # summarize measurements
            if t.timedout:
                m.setTimedout()
            elif rusage[1] == os.EX_OK:
                m.setOkay()
            else:
                m.setError()

            m.userSysTime = rusage[2][0] + rusage[2][1]
            m.maxMem = t.maxMem

            load = [
                int(round(100.0 * (1.0 - float(t1_idle - t0_idle) / (t1_total - t0_total))))
                for t0_total, t1_total, t0_idle, t1_idle in zip(cpus0, cpus1, cpus0_idle, cpus1_idle)
                if t0_total and t1_total
            ]

            # load.sort(reverse=1) # maybe more obvious unsorted
            m.cpuLoad = ("% ".join([str(i) for i in load])) + "%"
            m.elapsed = elapsed

        except KeyboardInterrupt:
            logger.exception('meassure')
            os.kill(p.pid, signal.SIGKILL)

        except ZeroDivisionError as xxx_todo_changeme1:
            logger.exception('meassure')
            (e, err) = xxx_todo_changeme1.args
            if logger:
                logger.warn('%s %s', err, 'too fast to measure?')

        except (OSError, ValueError) as xxx_todo_changeme2:
            logger.exception('meassure')
            (e, err) = xxx_todo_changeme2.args
            if e == ENOENT:  # No such file or directory
                if logger:
                    logger.warn('%s %s', err, commandline)
                m.setMissing()
            else:
                if logger:
                    logger.error('%s %s', e, err)
                m.setError()
        except Exception as e:
            logger.exception('meassure')
        finally:
            w.dump(m)
            wPipe.close()

            # Sample thread will be destroyed when the forked process _exits
            os._exit(0)
