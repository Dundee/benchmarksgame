# The Computer Language Benchmarks Game
# $Id: readme.txt,v 1.6 2013/04/21 15:40:58 igouy-guest Exp $

	bencher - June 2011, April 2013



 AUDIENCE

 Someone who wants to make repeated measurements of standalone 
 programs - cpu time, elapsed time, resident memory usage, 
 cpu load while a program is running.

 YOU ARE EXPECTED TO...

 Firstly, you are expected to use the bencher script to work through 
 each of the tutorial steps 1-6 to identify and fix configuration
 problems. 

 1) REQUIRED SOFTWARE
 2) RECOMMENDED SOFTWARE
 3) INSTALL
 4) FIXING PROBLEMS
 5) MAKING NEW MEASUREMENTS
 6) LOOKING AT THE MEASUREMENTS

 Secondly, you are expected to look for answers to other questions in 
 the examples given in sections 7-15.

 7) REMOVE (REINSTALL) 

 8) FILE EXTENSIONS & INI FILE PROPERTIES 

 9) HOW TO MEASURE A SEQUENCE OF WORKLOADS (OR JUST ONE WORKLOAD)
10) HOW TO MAKE REPEATED MEASUREMENTS (OR JUST ONE MEASUREMENT)

11) HOW TO ADD NEW VERSIONS OF PROGRAMS

12) HOW TO ADD NEW LANGUAGE IMPLEMENTATIONS
13) HOW TO ADD NEW LANGUAGE IMPLEMENTATIONS THAT REQUIRE MAKE

14) HOW TO ADD NEW KINDS OF PROGRAM
15) HOW TO SET DIFFERENT OPTIONS FOR DIFFERENT KINDS OF PROGRAM

 Thirdly, you are expected to have questions which are not answered here,
 and you are expected to ask those questions on the benchmarks game
 discussion forum --

 http://alioth.debian.org/forum/forum.php?forum_id=2965



 1) REQUIRED SOFTWARE

 Python 2.5+ (these are Python scripts)

 [ WIN32 SPECIFIC REQUIRED SOFTWARE ]

 Python Win32 Extensions (for win32 process monitoring)
   see http://sourceforge.net/projects/pywin32/

   (add your Python directory to the system path,
    for example \Python26 )



 2) RECOMMENDED SOFTWARE

 ndiff (to check program output more selectively)
    see http://www.math.utah.edu/~beebe/software/ndiff/

 highlight 3+: code & syntax highlighting
   see http://www.andre-simon.de/doku/highlight/en/highlight.html



 2.1) LINUX SPECIFIC RECOMMENDED SOFTWARE
 
 libgtop2 dev files and python-gtop Python bindings
    (for cpu load and resident memory measurement)

 python-gtop has been removed from recent distros, so I use: 
 
    http://packages.ubuntu.com/precise/python-gtop

 :and install with:

    dpkg -i python-gtop_2.32.0+dfsg-1_amd64.deb 

 GNU make (to compile programs for compiled languages)
 GNU diff & cmp (to check program output is as expected)

  

 2.2) [ WIN32 SPECIFIC RECOMMENDED SOFTWARE ]

 GNU Make for Windows
   see http://gnuwin32.sourceforge.net/packages/make.htm

 GNU DiffUtils for Windows
   see http://gnuwin32.sourceforge.net/packages/diffutils.htm

   (add GnuWin32\bin to the system path)

   (add highlight to the system path)



 3) LINUX INSTALL [ WIN32 INSTALL ]

 3.1) unzip in ~ directory [ unzip in c:\ ]

    The default configuration only creates files in the bencher
    subdirectory

 3.2) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Sun 15:49:01 .....OK .....nbody.java-2.java [3]
    Sun 15:49:03 .....OK .....nbody.python [2]
    Sun 15:49:12 .OK .....regexdna.python [1]

 3.3) Even if you did not see a problem, read section #4 now.



 4) FIXING PROBLEMS

 4.1) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Sun 16:44:37 .PROGRAM FAILED nbody.java-2.java [3]
    Sun 16:44:38 .....OK .....nbody.python [2]
    Sun 16:44:46 .OK .....regexdna.python [1]


    CHECK the log file of programs that were not OK   

    c:\bencher\tmp\nbody\log\nbody.2.java.log

    "MAKE:
     make program not found"

    "PROGRAM OUTPUT:
     java.lang.NoClassDefFoundError: nbody"

    The Java program has not been compiled because the make
    utility used to build programs has not been installed.


 4.2) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Sun 17:20:55 .MAKE ERROR nbody.java-2.java [3]
    Sun 17:20:55 .....OK .....nbody.python [2]
    Sun 17:21:05 .OK .....regexdna.python [1]

    CHECK the log file of programs that were not OK   

    ~/bencher/tmp/nbody/log/nbody.2.java.log

    "MAKE:
     mv nbody.java-2.java nbody.java
     javac nbody.java
     make: javac: Command not found"

    The Java program has not been compiled because javac cannot be
    found. CHECK the [tools] section of bencher/makefiles/my.*.ini 
    defines the correct path for javac.


 4.3) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Sun 17:33:41 .PROGRAM FAILED nbody.python [3]
    Sun 17:33:41 .....OK .....nbody.java-2.java [2]
    Sun 17:33:43 .OK .....regexdna.python [1]

    CHECK the log file of programs that were not OK  

    ~/bencher/tmp/nbody/log/nbody.1.python.log

    "PROGRAM OUTPUT:
     File "nbody.python", line 12
     DAYS_PER_YEAR = 365.24 z
                           ^
    SyntaxError: invalid syntax"

    There's a syntax error in the python program.



 5) MAKING NEW MEASUREMENTS

 5.1) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    nothing to be done - measurements are up-to-date

    New measurements are only made when there are no measurements to 
    match every program source code file, or when the measurement file
    is older than the program source code file.  
     

 5.2) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Sun 18:08:31 .....OK .....nbody.python [1]

    Fixing the syntax error in the python program, made the program 
    source code file newer than the measurement file. The measurement
    was out-of-date so the python program was measured again.


 5.3) $ python ~/bencher/bin/bencher.py nbody

    [ > python c:\bencher\bin\bencher.py nbody ]

    measure cpu & elapsed time & memory & cpu load
    Sun 18:12:50 .....OK .....nbody.java-2.java [2]
    Sun 18:13:01 .....OK .....nbody.python [1]

    New measurements can be forced by explicitly listing the tests 
    that should be measured again - in this case "nbody".


 5.4) $ python ~/bencher/bin/bencher.py python

    [ > python c:\bencher\bin\bencher.py python ]

    measure cpu & elapsed time & memory & cpu load
    Sun 18:19:22 .....OK .....nbody.python [2]
    Sun 18:19:31 .OK .....regexdna.python [1]

    New measurements can be forced by explicitly listing the language
    implementations (the program source code file extensions) that should
    be measured again - in this case "python".


 5.5) $ python ~/bencher/bin/bencher.py 

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Sun 18:22:41 .....OK .....nbody.python [1]

    New measurement of just one program can be forced by deleting the 
    corresponding measurement file and running the script - in this case
    "~/bencher/tmp/nbody/dat/nbody.1.python_dat" was deleted before running
    the script.
    
    

 6) LOOKING AT THE MEASUREMENTS

      ~/bencher/summary/all_measurements.csv

    [ c:\bencher\summary\all_measurements.csv ]

    Open all_measurements.csv with a text editor.


 6.1) The first 4 comma separated columns identify each measurement record
       1) test name 2) file extension 3) id# 4) data value

    For example, the measurement for the nbody.java-2.java program run
    with [testrange] value 20000 on the command line begins - 

       nbody,java,2,20000

    
 6.2) The last 6 comma separated columns provide
       5) size of the GZip compressed program source-code file in bytes
       6) program usr+sys rusage time in seconds
       7) memory used in KB
       8) status value, if OK then 0 otherwise a negative status value
       9) approximate CPU load on each core during measurement
      10) program elapsed time in seconds


 6.3) Notice there are repeated measurements at some data values.

   See section #9 "HOW TO MAKE REPEATED MEASUREMENTS (OR JUST ONE 
   MEASUREMENT)" below.


 6.4) For more details about how the measurements are made see -

   http://benchmarksgame.alioth.debian.org/play.php#measure 


 6.5) Filter and process the comma separated measurement files with
   a spreadsheet program or statistical analysis program or custom script.



 7) REMOVE (REINSTALL) 

   7.1) To remove - delete the bencher directory and bencher.zip file

   7.2) To reinstall - delete the bencher directory and then unzip
      the bencher.zip file again



 8) FILE EXTENSIONS & INI FILE PROPERTIES

   8.1) The source code file extensions and the names for different kinds
      of program link together source code, make command lines, run 
      command lines, measurement records, measurement logs and source code
      markup.

   8.2) Many bencher/makefiles/my.*.ini properties define values that will be
      substituted when the corresponding names appear in other properties in the
      ini file, or in bencher/makefiles/my.*.Makefile commands.

   8.3) Other bencher/makefiles/my.*.ini properties allow you to control which
      programs will be measured, how many times they will be measured, and when
      to timeout.



 9) HOW TO MEASURE A SEQUENCE OF WORKLOADS (OR JUST ONE WORKLOAD)

   9.1) Set one or more input values in the [testrange] section
      of bencher/makefiles/my.*.ini 

     For example, 5 input values are set for nbody programs and 1 input 
     value is set for regexdna programs -

        nbody = 10000 20000 30000 40000 50000
        regexdna = 10000 


   9.2) To provide program input from a data file redirected to stdin, set a path
      to the data file in the [testdata] section of bencher/makefiles/my.*.ini 

      Each of the input values set in the [testrange] section will
      successively be substituted into the data file path - so programs 
      can be measured working on different size data files that provide 
      increasing workloads.
      
      For example

         [testrange]
         regexdna = 10000 20000

         [testdata]
         regexdna = ../regexdna-input.txt

      will measure a regexdna program firstly with the contents of this
      data file redirected on the command line to program stdin

         ../regexdna-input10000.txt

      and then measure a regexdna program with the contents of this data
      file redirected on the command line to program stdin
     
         ../regexdna-input20000.txt


   9.3) To provide program input from one command line argument, set the 
      input data value in the [testrange] section of bencher/makefiles/my.*.ini

      For example

        [testrange]
        nbody = 10000 20000 30000 40000 50000

        [testdata]


      will measure an nbody program firstly with the string '10000' 
      substituted for %A in the command line set in the [commandlines]
      section of bencher/makefiles/my.*.ini

      Then measure an nbody program with the string '20000' substituted
      for %A in the command line, and then '30000' and then '40000' and
      then '50000'.


   9.4) To measure at just one workload, set just one value in the 
      [testrange] section of bencher/makefiles/my.*.ini 

      For example

        [testrange]
        nbody = 50000
        regexdna = 20000



10) HOW TO MAKE REPEATED MEASUREMENTS (OR JUST ONE MEASUREMENT)

   10.1) For example, to make 50 repeated measurements for every input value 
      use these settings in the [measure] section of bencher/makefiles/my.*.ini

        [measure]
        runs = 50
        repeatevery = True

   10.2) For example, to make just one measurement for every input value 
      use these settings in the [measure] section of bencher/makefiles/my.*.ini

        [measure]
        runs = 1
        repeatevery = True

   10.3) For example, to make 50 repeated measurements for the last input 
      value but just one measurement for every other (smaller) input 
      value use these settings:

        [measure]
        runs = 50
        repeatevery = False



11) HOW TO ADD NEW VERSIONS OF PROGRAMS

   Add the new versions of the program source code files to the appropriate
   subdirectories of bencher/programs, and run the bencher.py script.

 11.1) For example, in the bencher/programs/nbody directory, copy and rename
    the program source code file bencher/programs/nbody.java-2.java

    ~/bencher/programs/nbody$ cp nbody.java-2.java ./nbody.java-3.java


 11.2) $ python ~/bencher/bin/bencher.py

    [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Tue 19:55:07 .....OK .....nbody.java-3.java [1]


 11.3) Or edit the old versions of the program source code files and run the
    bencher script:

    measure cpu & elapsed time & memory & cpu load
    Tue 19:58:28 .....OK .....nbody.java-2.java [1]



12) HOW TO ADD NEW LANGUAGE IMPLEMENTATIONS

12.1) In the [tools] section of bencher/makefiles/my.*.ini, define a name
   that will substitute for the location of the new language implementation.
    
   For example

      [tools]

      PYTHON3 = /usr/local/src/Python-3.2/bin/python3.2


12.2) Choose a file extension to identify programs and measurements made 
   with the new language implementation, for example - python3.


12.3) In the [commandlines] section of bencher/makefiles/my.*.ini, define a
   command line that will be used to run program source code files that have
   the new file extension you chose.
    
   For example, for file extension python3

      [commandlines]

      python3 = $PYTHON3 %X %A


12.4.1) EITHER alias existing source code files that have a different file
   extension with the new file extension, in the [alias] section of 
   bencher/makefiles/my.*.ini

   For example, re-use all source code files with file extension python but
   make measurements identified with file extension python3 

      [alias]

      python = python3


12.4.2) OR add new program source code files written for the new language 
   implementation to the appropriate subdirectories of bencher/programs

   For example, to bencher/programs/nbody add

      nbody.python3


12.5) Run the bencher.py script.

     $ python ~/bencher/bin/bencher.py

   [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Wed 18:58:12 .....OK .....nbody.python3 [1]



13) HOW TO ADD NEW LANGUAGE IMPLEMENTATIONS THAT REQUIRE MAKE

13.1) In the [tools] section of bencher/makefiles/my.*.ini, define a name that
   will substitute for the location of the new language implementation.
    
   For example

      [tools]

      JDKRUN = /usr/local/src/jdk1.6.0_25/bin/java
      JDKC = /usr/local/src/jdk1.6.0_25/bin/javac


13.2) Choose a file extension to identify programs and measurements made 
   with the new language implementation, for example - javaxint.


13.3) In the [commandlines] section of bencher/makefiles/my.*.ini, define a 
   command line that will be used to run program source code files that have
   the new file extension you chose.
    
   For example, for file extension javaxint

      [commandlines]

      javaxint = $JDKRUN $JDKFLAGS -server -Xint %T %A


13.4) In the [build] section of bencher/makefiles/my.*.ini, add the new file
   extension to the list of source code files that require make.

   For example, for file extension javaxint

      [build]

      make =
       java javaxint


13.5) In bencher/makefiles/my.*.Makefile, add a make target with the commands
   required to prepare source code files for the new language implementation. 

   For example, for file extension javaxint

      %.javaxint_run: %.javaxint
            -mv $< $(TEST).java    # change .javaxint to .java
            -$(JDKC) $(TEST).java  # compile java source code


13.6.1) EITHER alias existing source code files that have a different file
   extension with the new file extension, in the [alias] section of 
   bencher/makefiles/my.*.ini

   For example, re-use all source code files with file extension java but
   make measurements identified with file extension javaxint 

      [alias]

      java = javaxint


13.6.2) OR add new program source code files written for the new language 
   implementation to the appropriate subdirectories of bencher/programs

   For example, to bencher/programs/nbody add

      nbody.javaxint-2.javaxint


13.7) Run the bencher.py script.

     $ python ~/bencher/bin/bencher.py

   [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Wed 20:26:38 .....OK .....nbody.javaxint-3.javaxint [2]
    Wed 20:26:42 .....OK .....nbody.javaxint-2.javaxint [1]



14) HOW TO ADD NEW KINDS OF PROGRAM

14.1) Add a new subdirectory for program source code files to the 
   bencher/programs directory


14.2) Add new program source code files with matching filenames to the
   new subdirectory

   For example, for a new kind of program named fannkuchredux

   bencher
      programs
         fannkuchredux (new subdirectory)
            fannkuchredux.java (new program source code file)


14.3) In the [filters] section of bencher/makefiles/my.*.ini, add the new
   subdirectory to the list of onlydirs that will be searched for source code
   files.

   For example, add the subdirectory fannkuchredux

      [filters]
      onlydirs = 
       nbody
       regexdna
       fannkuchredux


14.4) Input for the new kind of program can be either a single argument on
   the command line or a data file redirected to the programs stdin.

14.4.1) To provide a single argument on the command line, set the input values
   for the new kind of program in the [testrange] section 
   of bencher/makefiles/my.*.ini

   For example, add input values for fannkuchredux

      [testrange]
      nbody = 10000 20000 30000 40000 50000
      regexdna = 10000
      fannkuchredux = 10 11 12


14.4.2) To provide a data file redirected to the programs stdin, set both a path
   to the data file in the [testdata] section of bencher/makefiles/my.*.ini AND
   values that will be substituted into the filename AND add the data files
   to a subdirectory of bencher/tmp. 

   For example, for a new kind of program called knucleotide

      [testdata]
      knucleotide = ../knucleotide-input.txt

      [testrange]
      nbody = 10000 20000 30000 40000 50000
      regexdna = 10000
      knucleotide = 250000 2500000 25000000

   Add the new subdirectory and data files

   bencher
      tmp
         knucleotide (new subdirectory)
            knucleotide-input250000.txt (new data file)
            knucleotide-input2500000.txt (new data file)
            knucleotide-input25000000.txt (new data file)


14.5) Run the bencher.py script.

     $ python ~/bencher/bin/bencher.py

   [ > python c:\bencher\bin\bencher.py ]

    measure cpu & elapsed time & memory & cpu load
    Thu 09:05:31 ...OK .....fannkuchredux.java [2]
    Thu 09:07:18 ...OK .....knucleotide.java-3.java [1]



15) HOW TO SET DIFFERENT OPTIONS FOR DIFFERENT KINDS OF PROGRAM

15.1) Add a section to bencher/makefiles/my.*.ini

   For example, for the kind of program called nbody

    ; ENVIRONMENT for specific tests

    [nbody]


15.2) Add a property to set a name and value, for example

    ; ENVIRONMENT for specific tests

    [nbody]

    JDKFLAGS = -Xmx8m
    GCCOPTS = -lm -mfpmath=sse -msse3


15.3) Use that property name in a command line

   For example, for the kind of program called nbody substitute the value -Xmx8m
   into the command line set in bencher/makefiles/my.*.ini for programs with file 
   extension java

      [commandlines]

      java = $JDKRUN $JDKFLAGS -server %T %A

   In this case, nbody programs with file extension java will be run with
   command lines like

      $ java -Xmx8m -server nbody 50000000

   But other kinds of program with file extension java will still be run 
   with command lines like this

      $ java -server fannkuchredux 12


15.4) Use that property name in a make command

   For example, for the kind of program called nbody substitute the value 
   -lm -mfpmath=sse -msse3 into the make command set in 
   bencher/makefiles/my.*.Makefile for programs with file extension gcc

      STD_COPTS := -O3 -fomit-frame-pointer -march=native

      %.c: %.gcc $(GCC)
            -@mv $< $@

      %.gcc_run: %.c $(GCC)
            -$(GCC) -pipe -Wall $(STD_COPTS) $(GCCOPTS) $< -o $@

   In this case, nbody programs with file extension gcc will be compiled
   with command lines like

      $ gcc -pipe -Wall -O3 -fomit-frame-pointer -march=native 
            -lm -mfpmath=sse -msse3 nbody.gcc-5.c -o nbody.gcc-5.gcc_run

   and run with command lines like

      ./nbody.gcc-5.gcc_run 50000000

   But other kinds of program with file extension gcc will still be 
   compiled with command lines like this

      $ gcc -pipe -Wall -O3 -fomit-frame-pointer -march=native 
            fannkuchredux.c -o fannkuchredux.gcc_run

   and run with command lines like

      ./fannkuchredux.gcc_run 12

