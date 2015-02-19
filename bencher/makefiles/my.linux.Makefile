# The Computer Language Benchmarks Game
# $Id: my.linux.Makefile,v 1.1 2012/12/29 19:19:32 igouy-guest Exp $

# ASSUME each program will build in a clean empty tmpdir
# ASSUME there's a symlink to the program source in tmpdir
# ASSUME there's a symlink to the Include directory in tmpdir
# ASSUME there are symlinks to helper files in tmpdir
# ASSUME no responsibility for removing temporary files from tmpdir

# TYPICAL actions include an initial mv to give the expected extension

# ASSUME environment variables for compilers and interpreters are set in the header

########################################
# Cython
########################################

%.cython_run: %.cython
	$(eval NAME=`echo $< | sed 's/cython-..//' | sed 's/.cython//'`)
	cp $< $(NAME).py
	cythonize -3 -b $(NAME).py

