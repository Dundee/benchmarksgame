<?php
// Copyright (c) Isaac Gouy 2009-2010
ob_start('ob_gzhandler');
require_once('config.php'); 
$T = 'all'; $L = 'ats'; $metaRobots = '';
$LinkRelCanonical = '<link rel="canonical" href="http://benchmarksgame.alioth.debian.org/u64q/ats.php" />';
require_once(LIB_PATH.'compare.php');
?>
