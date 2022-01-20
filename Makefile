PYTHON = python

.PHONY: run clean update-results

run:
	sudo cpupower frequency-set -g performance
	cd bencher; ulimit -m 4000000; $(PYTHON) ./bin/bencher.py
	sudo cpupower frequency-set -g powersave

clean:
	rm -R bencher/tmp/*
	mkdir -p bencher/tmp/knucleotide
	mkdir -p bencher/tmp/regexdna
	mkdir -p bencher/tmp/revcomp
	cp bencher/data/knucleotide-input10000.txt bencher/tmp/knucleotide/
	cp bencher/data/regexdna-input10000.txt bencher/tmp/regexdna/
	cp bencher/data/revcomp-input1000.txt bencher/tmp/revcomp/

update-results:
	cp bencher/tmp/all_measurements.csv ./website/websites/u64q/data/data.csv
	cp bencher/tmp/all_measurements.csv ./website/websites/u64q/data/ndata.csv
	rm ./website/websites/u64q/code/*
	cp bencher/tmp/*/code/*.code ./website/websites/u64q/code
	cp bencher/tmp/*/log/*.log ./website/websites/u64q/code

%.cython_run: %.cython
	$(eval NAME=`echo $< | sed 's/cython-..//' | sed 's/.cython//'`)
	cp $< $(NAME).pyx
	cythonize -3 -bia $(NAME).pyx

prepare: prepare-python prepare-python3 prepare-python-dev prepare-pypy \
	prepare-pypy3 prepare-ipy prepare-jython prepare-cython prepare-nuitka \
	prepare-shedskin prepare-numba prepare-pyston prepare-micropython prepare-grumpy

prepare-python:
	sudo pacman -S python2-gmpy2 python2-numpy python2-jinja

prepare-python3:
	sudo pacman -S python-gmpy2 python-numpy python-jinja

prepare-python-dev:
	yaourt -S python-git
	sudo python3.11 -m pip install jinja2 gmpy2 numpy

prepare-pypy:
	sudo pacman -S pypy
	yaourt -S pypy-pip
	sudo pypy -m pip install jinja2 gmpy_cffi numpy

prepare-pypy3:
	sudo pacman -S pypy3
	yaourt -S pypy3-pip
	sudo pypy3 -m pip install jinja2 gmpy_cffi numpy

prepare-ipy:
	yaourt -S ironpython-git

prepare-jython:
	sudo pacman -S jdk8-openjdk jython
	ln -s /opt/jython/bin/jython /usr/bin/jython
	sudo jython -m pip install jinja2

prepare-cython:
	sudo pacman -S cython

prepare-nuitka:
	sudo pacman -S nuitka

prepare-shedskin:
	sudo pacman -S shedskin

prepare-numba:
	yaourt -S python-numba

prepare-pyston:
	yaourt -S pyston
	# manual install of setuptools, jinja2 and numpy

prepare-micropython:
	yaourt -S micropython

prepare-grumpy:
	yaourt -S grumpy-git

