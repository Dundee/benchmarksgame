<?php
// Copyright (c) Isaac Gouy 2010-2011

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
            "gnat" => "http://benchmarksgame.alioth.debian.org/u64q/ada.php",
            "ats" => "http://benchmarksgame.alioth.debian.org/u64q/ats.php",
            "gcc" => "http://benchmarksgame.alioth.debian.org/u64q/c.php",
            "clean" => "http://benchmarksgame.alioth.debian.org/u64/clean.php",
            "csharp" => "http://benchmarksgame.alioth.debian.org/u64q/csharp.php",
            "gpp" => "http://benchmarksgame.alioth.debian.org/u64q/erlang.php",
            "hipe" => "http://benchmarksgame.alioth.debian.org/u64q/ada.php",
            "fsharp" => "http://benchmarksgame.alioth.debian.org/u64q/fsharp.php",
            "ifc" => "http://benchmarksgame.alioth.debian.org/u64q/fortran.php",
            "ghc" => "http://benchmarksgame.alioth.debian.org/u64q/haskell.php",
            "java" => "http://benchmarksgame.alioth.debian.org/u64q/java.php",
            "v8" => "http://benchmarksgame.alioth.debian.org/u64/javascript.php",
            "sbcl" => "http://benchmarksgame.alioth.debian.org/u64q/lisp.php",
            "lua" => "http://benchmarksgame.alioth.debian.org/u64/lua.php",
            "oz" => "http://benchmarksgame.alioth.debian.org/u32/oz.php",
            "ocaml" => "http://benchmarksgame.alioth.debian.org/u64q/ocaml.php",
            "fpascal" => "http://benchmarksgame.alioth.debian.org/u64q/fpascal.php",
            "perl" => "http://benchmarksgame.alioth.debian.org/u64q/perl.php",
            "php" => "http://benchmarksgame.alioth.debian.org/u64q/php.php",
            "python3" => "http://benchmarksgame.alioth.debian.org/u64q/python.php",
            "racket" => "http://benchmarksgame.alioth.debian.org/u64q/racket.php",
            "yarv" => "http://benchmarksgame.alioth.debian.org/u64q/ruby.php",
            "jruby" => "http://benchmarksgame.alioth.debian.org/u64q/jruby.php",
            "scala" => "http://benchmarksgame.alioth.debian.org/u64q/scala.php",
            "vw" => "http://benchmarksgame.alioth.debian.org/u64/smalltalk.php"
         );

         if (isset($clinks[$L])){
            $cl = $clinks[$L];
            $LinkRelCanonical = '<link rel="canonical" href="'.$cl.'" />';
         }

         require_once(LIB_PATH.'compare.php');
      } else {
        require_once(LIB_PATH.'measurements.php');
      }
   }
} elseif ($L=='all'){
   require_once(LIB_PATH.'performance.php');
} else {
   require_once(LIB_PATH.'program.php');
}
?>



