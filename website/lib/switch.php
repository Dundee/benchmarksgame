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

function Alias($l){ // hardcode aliases for old names
   return $l == "python" ? "python3" :
         ($l == "ruby" ? "yarv" :
         ($l == "javascript" ? "v8" :
         ($l == "mzscheme" ? "racket" :
         ($l == "javaxint" ? "java" :
         ($l == "javasteady" ? "java" : $l)))));
}

if (isset($_GET['lang'])
      && strlen($_GET['lang']) && (strlen($_GET['lang']) <= $NAME_LEN)){
   $X = $_GET['lang'];
   if (ereg("^[a-z0-9]+$",$X)){ $L = Alias($X); }
}
if (!isset($L)){ $L = 'all'; }


if (isset($_GET['lang2'])
      && strlen($_GET['lang2']) && (strlen($_GET['lang2']) <= $NAME_LEN)){
   $X = $_GET['lang2'];
   if (ereg("^[a-z0-9]+$",$X)){ $L2 = Alias($X); }
}
if (!isset($L2)){
   if ($L!='all'){ $L2 = ''; }
}


// PAGES ///////////////////////////////////////////////////

if ($T=='all'){
   if ($L=='all'){
      $LinkRelCanonical = '<link rel="canonical" href="http://benchmarksgame.alioth.debian.org/u64/which-programs-are-fastest.php" />';
      require_once(LIB_PATH.'boxplot.php');
   } else {
      if ($L!=$L2){

         // canonical links for Google
         $clinks = array(
            "gnat" => "u64q/ada.php",
            "ats" => "u64q/ats.php",
            "gcc" => "u64q/c.php",
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

        $LinkRelCanonical = '<link rel="canonical" href="http://benchmarksgame.alioth.debian.org/u32/performance.php?test='.$T.'" />';

   require_once(LIB_PATH.'performance.php');

} else {

   require_once(LIB_PATH.'program.php');
}
?>



