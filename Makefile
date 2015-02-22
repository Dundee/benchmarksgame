PYTHON = python2

run:
	cd bencher; $(PYTHON) ./bin/bencher.py

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


.PHONY: run clean update-results
