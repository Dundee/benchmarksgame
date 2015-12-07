<?php
// Copyright (c) Isaac Gouy 2009,2013

if (isset($_GET['data'])
      && strlen($_GET['data']) && (strlen($_GET['data']) <= 4)){
   $X = $_GET['data'];
   if ($X=='u64q'){ $D = $X; }
}

if (!isset($D)||($D=='u64q')){
   ob_start('ob_gzhandler');
   require_once('config.php');
   require_once(LIB_PATH.'switch.php');
} else {
   header('Location: https://pybenchmarks.org/'.$D.'/benchmark.php?'.$_SERVER['QUERY_STRING']);
   exit;
}
?>

