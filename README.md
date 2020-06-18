# Python Interpreters Benchmarks

This project aims to compare different Python interpreters using wide set of benchmarks.

## Structure of the project

* bencher/programs - source codes of benchmarks
* bencher/bin - python scripts to make repeated measurements of benchmarks
* bencher/makefiles - configuration for running benchmarks
* website - PHP scripts and configuration used to generate the [pybenchmarks.org website](https://pybenchmarks.org/)

Currently only Linux is supported for running benchmarks.

## How to run benchmarks

### Run one benchmark for all interpreters

1. do some changes in source code of some benchmark
2. run this benchmark:

```
make run
```

### Run all benchmarks for one interpreter

```
./clear_lang.sh <name_of_interpreter>  # e.g. cython
make run
```


