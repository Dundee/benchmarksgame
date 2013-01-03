<?php
// Copyright (c) Isaac Gouy 2010-2013

// DATA LAYOUT ///////////////////////////////////////////////////

$NAME_LEN = 16;
$LANG_COMPARE = 8;

// GET_VARS ////////////////////////////////////////////////

if (isset($_GET['test'])
      && strlen($_GET['test']) && (strlen($_GET['test']) <= $NAME_LEN)){
   $X = $_GET['test'];
   if (ereg("^[a-z]+$",$X)){ $T = $X; }
}
if (!isset($T)){ $T = 'all'; }

if (isset($_GET['lang'])
      && strlen($_GET['lang']) && (strlen($_GET['lang']) <= $NAME_LEN)){
   $X = $_GET['lang'];
   if (ereg("^[a-z0-9]+$",$X)){ $L = $X; }
}
if (!isset($L)){ $L = 'all'; }


if ($L2 == 'lang'){ $L2 = $L; }
else {
   if (isset($_GET['lang2'])
         && strlen($_GET['lang2']) && (strlen($_GET['lang2']) <= $NAME_LEN)){
      $X = $_GET['lang2'];
      if (ereg("^[a-z0-9]+$",$X)){ $L2 = $X; }
   }
}
if (!isset($L2)){
   if ($L!='all'){ $L2 = $Langs[$L][$LANG_COMPARE]; }
}


// PAGES ///////////////////////////////////////////////////

if ($T=='all'){
   if ($L=='all'){
      $LinkRelCanonical = '<link rel="canonical" href="which-programs-are-fastest.php" />';
      require_once(LIB_PATH.'boxplot.php');
   } else {
      if ($L!=$L2){

         // canonical links for Google
         $clinks = array(
            "gnat" => "u64q/ada.php",
            "ats" => "u64q/ats.php",
            "gcc" => "u64q/c.php",
            "clean" => "u64/clean.php",
            "csharp" => "u64q/csharp.php",
            "gpp" => "u64q/cpp.php",
            "clojure" => "u64q/clojure.php",
            "dart" => "u64q/dart.php",
            "hipe" => "u64q/erlang.php",
            "fsharp" => "u64q/fsharp.php",
            "ifc" => "u64q/fortran.php",
            "go" => "u64q/go.php",
            "ghc" => "u64q/haskell.php",
            "java" => "u64q/java.php",
            "v8" => "u64/javascript.php",
            "sbcl" => "u64q/lisp.php",
            "lua" => "u64/lua.php",
            "oz" => "u32/oz.php",
            "ocaml" => "u64q/ocaml.php",
            "fpascal" => "u64q/pascal.php",
            "perl" => "u64q/perl.php",
            "php" => "u64q/php.php",
            "python3" => "u64q/python.php",
            "racket" => "u64q/racket.php",
            "yarv" => "u64q/ruby.php",
            "jruby" => "u64q/jruby.php",
            "scala" => "u64q/scala.php",
            "vw" => "u64/smalltalk.php"
         );

         if (isset($clinks[$L])){
            $LinkRelCanonical = '<link rel="canonical" href="http://benchmarksgame.alioth.debian.org/'.$clinks[$L].'" />';
         }

         require_once(LIB_PATH.'compare.php');
      } else {
        require_once(LIB_PATH.'measurements.php');
      }
   }
} elseif ($L=='all'){

         // canonical links for Google
         $clinks = array(
            "binarytrees" => "performance.php?test=binarytrees",
            "binarytreesredux" => "performance.php?test=binarytreesredux",
            "chameneosredux" => "performance.php?test=chameneosredux",
            "fannkuchredux" => "performance.php?test=fannkuchredux",
            "fasta" => "performance.php?test=fasta",
            "fastaredux" => "performance.php?test=fastaredux",
            "knucleotide" => "performance.php?test=knucleotide",
            "mandelbrot" => "performance.php?test=mandelbrot",
            "meteor" => "performance.php?test=meteor",
            "nbody" => "performance.php?test=nbody",
            "pidigits" => "performance.php?test=pidigits",
            "regexdna" => "performance.php?test=regexdna",
            "revcomp" => "performance.php?test=revcomp",
            "spectralnorm" => "performance.php?test=spectralnorm",
            "threadring" => "performance.php?test=threadring"
         );

   $LinkRelCanonical = '<link rel="canonical" href="'.$clinks[$T].'" />';

   require_once(LIB_PATH.'performance.php');
} else {
   require_once(LIB_PATH.'program.php');
}
?>



