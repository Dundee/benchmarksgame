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
      $LinkRelCanonical = '<link rel="canonical" href="http://python.milde.cz/u64/which-programs-are-fastest.php" />';
      require_once(LIB_PATH.'boxplot.php');
   } else {
      if ($L!=$L2){

         // canonical links for Google
         $clinks = array(
            "php" => "u64q/php.php",
            "python3" => "u64q/python.php",
            "jruby" => "u64q/ruby.php",
         );

         if (isset($clinks[$L])){
            $LinkRelCanonical = '<link rel="canonical" href="http://python.milde.cz/'.$clinks[$L].'" />';
         }

         require_once(LIB_PATH.'compare.php');

      } else {

        require_once(LIB_PATH.'measurements.php');
      }
   }
} elseif ($L=='all'){

        $LinkRelCanonical = '<link rel="canonical" href="http://python.milde.cz/u32/performance.php?test='.$T.'" />';

   require_once(LIB_PATH.'performance.php');

} else {

   require_once(LIB_PATH.'program.php');
}
?>



