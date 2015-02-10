# The Computer Language Benchmarks Game
# $Id: u64q.programs.Makefile,v 1.4 2013/05/14 06:22:01 igouy-guest Exp $

# ASSUME each program will build in a clean empty tmpdir
# ASSUME there's a symlink to the program source in tmpdir
# ASSUME there's a symlink to the Include directory in tmpdir
# ASSUME there are symlinks to helper files in tmpdir
# ASSUME no responsibility for removing temporary files from tmpdir

# TYPICAL actions include an initial mv to give the expected extension

# ASSUME environment variables for compilers and interpreters are set in the header


SPLITFILE := $(NANO_BIN)/split_file.bash


########################################
# Python
########################################

%.python_run: %.python $(PYTHON)
	-mv $< $*.py
	-$(PYTHON) -OO -c "from py_compile import compile; compile('$*.py')"


########################################
# Psyco
########################################

%.psyco_run: %.psyco $(PYTHON)
	-mv $< $*.py
	-$(PSYCO) -OO -c "from py_compile import compile; compile('$*.py')"


########################################
# Python3
########################################

%.python3_run: %.python3 $(PYTHON3)
	-mv $< $*.py
	-$(PYTHON3) -OO -c "from py_compile import compile; compile('$*.py')"
