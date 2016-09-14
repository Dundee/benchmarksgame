<?php
// Copyright (c) Isaac Gouy 2009-2011

// DATA LAYOUT ///////////////////////////////////////////////////

define('TEST_LINK',0);
define('TEST_NAME',1);
define('TEST_TAG',2);
define('TEST_WEIGHT',3);
define('TEST_META',4);

define('LANG_LINK',0);
define('LANG_FULL',1);
define('LANG_HTML',2);
define('LANG_TAG',3);
define('LANG_SELECT',4);
define('LANG_COMPARE',5);
define('LANG_SPECIALURL',6);


// MEASUREMENT_RESCALE has to be big enough to rescale NO_MEASUREMENT
// so the NO_MEASUREMENT information is passed to charts
define('NO_VALUE',0.00001);
define('VALUE_RESCALE',100000.0);
define('VALUE_SHIFT',5);

define('NAME_LEN',16);
define('PRG_ID_LEN',NAME_LEN+2);

// FUNCTIONS ///////////////////////////////////////////////////


function SetChartCacheControl(){
   // website content changes at-most once an hour - say ten past the hour
   $m = floor(time()/60); $h = floor($m/60); $after_the_hour = $m - $h*60; $countdown = 10;
   if ($countdown <= $after_the_hour) { $countdown += 60; $h++; }
   header("Pragma: public");
   header("Cache-Control: maxage=".($countdown - $after_the_hour)*60);
   header("Expires: " . gmdate("D, d M Y H:i:s", $h*3600 + 600) . " GMT");
}


function WhiteListInEx(){
   return array(WhiteListIn(),WhiteListEx());
}

function WhiteListIn(){
   $incl = array();
   $lines = @file('./include.csv') or die('Cannot open ./include.csv');
   // assume no header line
   foreach($lines as $line) {
      $incl[ chop($line) ] = TRUE;
   }
   return $incl;
}

function WhiteListEx(){
   $excl = array();
   $lines = @file(DESC_PATH.'exclude.csv') or die('Cannot open '.DESC_PATH.'exclude.csv');
   // assume no header line
   $slash = ord('\\');
   foreach($lines as $line) {
      // programs which should never be shown have a '\' prefix in excl.csv
      $value = strpos($line,$slash)>0 ? FALSE : TRUE;
      $excl[ stripslashes(chop($line)) ] = $value;
   }
   return $excl;
}



function WhiteListUnique($FileName,$Incl){
   $lines = @file(DESC_PATH.$FileName) or die ('Cannot open $FileName');
   // assume no header line
   $rows = array();
   foreach($lines as $line) {
      $row = explode( ',', $line);
//      if (!is_array($row)){ continue; }
//######## Hardcoded assumption that $row[0] is a link name
      if (isset( $Incl[$row[0]] )){ $rows[ $row[0] ] = $row; }
   }
   return $rows;
}

   // VALIDATION

function Encode($x){
   $s = "";
   if (is_string($x)){  // simple string
      $s = $x;
   } elseif (is_array($x)){
      if (sizeof($x)>0){
         $matrix = array();
         if (is_numeric($x[0])){ // single array of doubles
            $matrix = &$x;
         } elseif (is_array($x[0])){ // array of array of doubles
            foreach($x as $each){ $matrix = array_merge($matrix,$each); }
         }
         if (sizeof($matrix)>0){
            foreach($matrix as $v){
               $z = $v <= NO_VALUE ? NO_VALUE : $v;
               $d[] = intval(sprintf('%d',(log10($z)+VALUE_SHIFT)*VALUE_RESCALE));
            }
            $x = $d;
         }
      }
      $s = implode('O',$x);
   }
   return rawurlencode(base64_encode(gzcompress($s,9)));
}


function ValidMark($valid=FALSE){
   $bounds = 64;
   $mark = '';
   if ($valid){
      $valid = FALSE;
      if (isset($_GET['m']) && strlen($_GET['m']) && strlen($_GET['m']) <= $bounds){
         $X = base64_decode( rawurldecode($_GET['m']) );
         $X = @gzuncompress($X,$bounds); // returns FALSE on error
         if ($X && preg_match("~^[ a-zA-Z0-9]+$~",$X)){
            $mark = $X;
            $valid = TRUE;
         }
      }
   }
   return array($mark,$valid);
}

function ValidSort($valid=FALSE){
   $bounds = 32; // base64_encoded size
   $sort = '';
   if ($valid){
      $valid = FALSE;
      if (isset($_GET['so']) && strlen($_GET['so']) && strlen($_GET['so']) <= $bounds){
         $X = base64_decode( rawurldecode($_GET['so']) );
         $X = @gzuncompress($X,$bounds); // returns FALSE on error
         if ($X && ereg("^[a-z]+$",$X)
           && (($X == 'elapsed') || ($X == 'gz') || ($X == 'kb') || ($X == 'fullcpu')) ){
               $sort = $X;
               $valid = TRUE;
         }
      }
   }
   return array($sort,$valid);
}


function ValidMatrix($V,$size,$valid=FALSE){
   $bounds = 1024;
   $d = array();
   if ($valid){
      $valid = FALSE;
      if (isset($_GET[$V]) && strlen($_GET[$V]) && strlen($_GET[$V]) <= $bounds){
         $X = base64_decode( rawurldecode($_GET[$V]) );
         $X = @gzuncompress($X,$bounds); // returns FALSE on error

         if ($X && preg_match("~^[0-9O]+$~",$X)){
            foreach(explode('O',$X) as $v){
               if (strlen($v) && (strlen($v) <= 10) && is_numeric($v)){
                  $d[] = pow(10.0,(doubleval($v)/VALUE_RESCALE - VALUE_SHIFT));
               } else {
                  $d = array();
                  break;
               }
            }
            if ((sizeof($d)%$size) == 0){ $valid = TRUE;
            } else { $d = array(); }
         }
      }
   }
   return array($d,$valid);
}


function ValidLangs($Langs,$valid=FALSE){
   return ValidWhiteList('w',$Langs,"~^[a-z0-9O-]+$~",24,LANG_FULL,$valid);
}

function ValidTests($Tests,$valid=FALSE){
   return ValidWhiteList('ww',$Tests,"~^[a-zO]+$~",32,TEST_NAME,$valid);
}

// private
function ValidWhiteList($V,$WhiteList,$regex,$size,$index,$valid){
   $bounds = 512;
   $d = array();
   if ($valid){
      $valid = FALSE;
      if (isset($_GET[$V]) && strlen($_GET[$V]) && strlen($_GET[$V]) <= $bounds){
         $X = base64_decode( rawurldecode($_GET[$V]) );
         $X = @gzuncompress($X,$bounds); // returns FALSE on error

         if ($X && preg_match($regex,$X)){
            foreach(explode('O',$X) as $v){
               if (strlen($v) && (strlen($v) <= $size) && isset($WhiteList[$v])){
                  $d[] = $WhiteList[$v][$index];
               } else {
                  $d = array();
                  break;
               }
            }
            $valid = TRUE;
         }
      }
   }
   return array($d,$valid);
}


function HtmlFragment($FileName){
   $html = '';
   if (is_readable($FileName)){
      $f = fopen($FileName,'r');
      $fs = filesize($FileName);
      if ($fs > 0){ $html = fread($f,$fs); }
      fclose($f);
   }
   return $html;
}

?>
