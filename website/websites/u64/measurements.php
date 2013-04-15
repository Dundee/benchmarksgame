<?php
// Copyright (c) Isaac Gouy 2010,2013

if (isset($_GET['data'])
      && strlen($_GET['data']) && (strlen($_GET['data']) <= 4)){
   $X = $_GET['data'];
   if ($X=='u32'||($X=='u64q'||($X=='u32q'||($X=='u64')))){ $D = $X; }
}

if (!isset($D)||($D=='u64')){
   ob_start('ob_gzhandler');
   require_once('config.php');
   require_once(LIB_PATH.'measurements.php');
} else {
   header('Location: http://benchmarksgame.alioth.debian.org/'.$D.'/measurements.php?'.$_SERVER['QUERY_STRING']);
   exit;
}
?>
