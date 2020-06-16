# The Computer Language Benchmarks Game
# $Id: uselinux.py,v 1.1 2012/12/29 19:19:31 igouy-guest Exp $

import gi

gi.require_version('GTop', '2.0')
from gi.repository import GTop
from planA import measure

planDesc = 'measure cpu & elapsed time & memory & cpu load'


# =============================
# global variables
# =============================


nullName = '/dev/null'


# =============================
# initialize & configure
# =============================

import os
from errno import EEXIST
from os.path import join


def linkToSource(directory, filename, dstDir=None, srcFilename=None):
    # beware - read carefully
    src = join(directory, filename) if srcFilename == None else join(directory, srcFilename)

    # tmpdir is $CWD
    dst = join(filename) if dstDir == None else join(dstDir, filename)

    try:
        os.symlink(src, dst)
    except OSError as xxx_todo_changeme:
        (e, _) = xxx_todo_changeme.args
        if e == EEXIST:
            pass  # OK the symlink already exists - or should it be removed?


def linkToIncludeDir(directory, filename, dstDir=None, srcFilename=None):
    linkToSource(directory, filename)
