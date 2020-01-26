<?php
// Copyright (c) Isaac Gouy 2004-2012

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);

// DATA ///////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl);

if (isset($_GET['test'])
      && strlen($_GET['test']) && (strlen($_GET['test']) <= NAME_LEN)){
   $X = $_GET['test'];
   if (preg_match("~^[a-z]+$~",$X) && (isset($Tests[$X]) || $X == 'all')){ $T = $X; }
}
if (!isset($T)){ $T = 'nbody'; }


if (isset($_GET['file'])
      && strlen($_GET['file']) && (strlen($_GET['file']) <= 6)){
   $X = $_GET['file'];
   if (preg_match("~^[a-z]+$~",$X) && ($X == 'input' || $X == 'extra')){ $F = $X; }
}
if (!isset($F)){ $F = 'output'; }

if (!isset($E)){ $E = 'txt'; }


$TestName = $Tests[$T][TEST_NAME];

if ($F == 'input'){ $Title = $TestName.' input file'; }
elseif ($F == 'output'){ $Title = $TestName.' output file'; }
elseif ($F == 'extra'){ $Title = $TestName.' file'; }
else { $Title = $TestName; }

$faqUrl = CORE_SITE.'play.php';
$bannerUrl = CORE_SITE;


// TEMPLATE VARS ////////////////////////////////////////////////

$Page = new Template(LIB_PATH);
$Page->set('PageTitle', $Title.BAR.'Python&nbsp;Interpreters&nbsp;Benchmarks&nbsp;Game');
$Page->set('BannerTitle', BANNER_TITLE);
$Page->set('FaqTitle', FAQ_TITLE);
$Page->set('BannerUrl', $bannerUrl);
$Page->set('FaqUrl', $faqUrl);
$Page->set('PageBody', BLANK);

$Body = new Template(LIB_PATH);
$Body->set('Title', $Title);
$Body->set('Download', DOWNLOAD_PATH.$T.SEPARATOR.$F.'.'.$E);
$Body->set('Text', HtmlFragment( DOWNLOAD_PATH.$T.SEPARATOR.$F.'.'.$E ));

$Page->set('PageBody', $Body->fetch('iofile.tpl.php'));
$Page->set('Robots', '<meta name="robots" content="noindex,nofollow,noarchive" />');
$Page->set('MetaKeywords', '');
$Page->set('PageId', 'iofile');
echo $Page->fetch('page.tpl.php');
?>

