<?php
// Copyright (c) Isaac Gouy 2009-2012

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB_PATH.'lib_common.php');
require_once(LIB);
require_once(LIB_PATH.'lib_scorecard.php');

// FUNCTIONS ///////////////////////////////////////////

function SelectedLangs($Langs, $Action){
   $w = array(); $wd = array();
   foreach($Langs as $lang){
      $link = $lang[LANG_LINK];
      if (isset($_GET[$link])){ $w[$link] = 1; }
      if ($lang[LANG_SELECT]){ $wd[$link] = 1; }
   }
   if ($Action=='reset'||sizeof($w)<=0){ $w = $wd; }
   return $w;
}


// PAGE ////////////////////////////////////////////////

$Page = & new Template(LIB_PATH);
$Body = & new Template(LIB_PATH);
$PageId = 'scorecard';
$TemplateName = 'scorecard.tpl.php';


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order

if (isset($_GET['calc'])
      && strlen($_GET['calc']) && (strlen($_GET['calc']) <= 9)){
   $X = $_GET['calc'];
   if (ereg("^[a-z]+$",$X) && ($X == 'reset')){ $Action = $X; }
}
if (!isset($Action)){ $Action = 'calculate'; }

$SLangs = SelectedLangs($Langs, $Action);


if (isset($_GET['d'])
      && strlen($_GET['d']) && (strlen($_GET['d']) <= 5)){
   $X = $_GET['d'];
   if (ereg("^[a-z]+$",$X) && ($X == 'ndata')){ $DataSet = $X; }
}
if (!isset($DataSet)||isset($Action)&&$Action=='reset'){ $DataSet = 'data'; }

$W = Weights($Tests, $Action, $_GET);


// HEADER ////////////////////////////////////////////////

$mark = MarkTime();
$mark = $mark.' '.SITE_NAME;
$Title = 'Which programs are best?';

$faqUrl = CORE_SITE.'play.php';
$bannerUrl = CORE_SITE;

// DATA ////////////////////////////////////////////////

$Data = FullWeightedData(DATA_PATH.$DataSet.'.csv', $Tests, $Langs, $Incl, $Excl, $W, $SLangs);

$timeUsed = 'Elapsed secs';


// ABOUT ////////////////////////////////////////////////

$About = & new Template(ABOUT_PATH);
$AboutTemplateName = 'scorecard-about.tpl.php';


// META ////////////////////////////////////////////////

$metaRobots = '<meta name="robots" content="noindex,follow,noarchive" />';
$MetaKeywords = '<meta name="description" content="Compare programming language performance using your choice of benchmarks &amp; Time-used Memory-used Code-used weights ('.PLATFORM_NAME.')." />';


// TEMPLATE VARS ////////////////////////////////////////////////

$Page->set('PageTitle', $Title.BAR.'Python&nbsp;Interpreters&nbsp;Benchmarks&nbsp;Game');
$Page->set('BannerTitle', BANNER_TITLE);
$Page->set('FaqTitle', FAQ_TITLE);
$Page->set('BannerUrl', $bannerUrl);
$Page->set('FaqUrl', $faqUrl);


$Body->set('Title', $Title);
$Body->set('DataSet', $DataSet);
$Body->set('W', $W);
$Body->set('Data', $Data);
$Body->set('Tests', $Tests);
$Body->set('Langs', $Langs);
$Body->set('Excl', $Excl);
$Body->set('Mark', $mark );

$About->set('DataSet', $DataSet);
$Body->set('About', $About->fetch($AboutTemplateName));

$Page->set('PageBody', $Body->fetch($TemplateName));
$Page->set('Robots', $metaRobots);
$Page->set('MetaKeywords', $MetaKeywords);
$Page->set('PageId', $PageId);

echo $Page->fetch('page.tpl.php');
?>
