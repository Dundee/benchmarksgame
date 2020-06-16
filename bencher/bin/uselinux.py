import os
from errno import EEXIST
from os.path import join

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


def linkToSource(directory, filename, dstDir=None, srcFilename=None):
    # beware - read carefully
    src = join(directory, filename) if srcFilename is None else join(directory, srcFilename)

    # tmpdir is $CWD
    dst = join(filename) if dstDir is None else join(dstDir, filename)

    try:
        os.symlink(src, dst)
    except OSError as xxx_todo_changeme:
        (e, _) = xxx_todo_changeme.args
        if e == EEXIST:
            pass  # OK the symlink already exists - or should it be removed?


def linkToIncludeDir(directory, filename, dstDir=None, srcFilename=None):
    linkToSource(directory, filename)
