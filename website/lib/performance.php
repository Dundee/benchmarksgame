<?php
// Copyright (c) Isaac Gouy 2010-2011

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB);
require_once(LIB_PATH.'lib_data.php');


// FUNCTIONS ///////////////////////////////////////////

function SelectedLangs($Langs){
   $w = array();
   foreach($Langs as $lang){
      $link = $lang[LANG_LINK];
      if (isset($_GET[$link])){ $w[$link] = 1; }
      if ($lang[LANG_SELECT]){ $wd[$link] = 1; }
   }
   if (sizeof($w)<=0){ $w = $wd; }
   return $w;
}

function BenchmarkData($FileName,$Test,$Langs,$Incl,$Excl,$Sort,$SLangs,$HasHeading=TRUE){
   $lines = @file($FileName) or die ('Cannot open $FileName');
   if ($HasHeading){ unset($lines[0]); } // remove header line

   $prefix = substr($Test,1).',';
   $succeeded = array();
   $failed = array();
   $special = array();

   $time_min = 360000.0; // 100 hours
   $mem_min = 1024000000;
   $gz_min = 1048576;
   $DATA_TIME_SORT = $Sort=='fullcpu' ? DATA_FULLCPU : DATA_TIME;

   foreach($lines as $line) {
      if (strpos($line,$prefix)){
         $row = explode( ',', $line);
         $lang = $row[DATA_LANG];

         if (isset($Incl[$lang])){
               //$exclude = $Excl[ $Test.$lang.$row[DATA_ID] ];
            $exclude = NULL;
            if (isset($Excl[ $Test.$lang.$row[DATA_ID] ])){
               $exclude = $Excl[ $Test.$lang.$row[DATA_ID] ];
            }
            if (!$exclude){
               settype($row[DATA_ID],'integer');
               settype($row[DATA_TESTVALUE],'integer');
               settype($row[DATA_GZ],'integer');
               settype($row[DATA_FULLCPU],'double');
               settype($row[DATA_MEMORY],'integer');
               settype($row[DATA_STATUS],'integer');
               settype($row[DATA_ELAPSED],'double');

               if (isset($exclude)){
                  $special[] = $row;
               } elseif ($row[DATA_STATUS]){
                  $failed[] = $row;
               } else {
                  $succeeded[] = $row;

                  $row_time = $row[$DATA_TIME_SORT];
                  if ($row_time > 0.0 && $row_time < $time_min){
                     $time_min = $row_time;
                  }
                  $row_mem = $row[DATA_MEMORY];
                  if ($row_mem > 0 && $row_mem < $mem_min){
                     $mem_min = $row_mem;
                  }
                  $row_gz = $row[DATA_GZ];
                  if ($row_gz > 0 && $row_gz < $gz_min){
                     $gz_min = $row_gz;
                  }
               }

            }
         }
      }
   }

   if ($Sort=='fullcpu'){
      usort($succeeded, 'CompareFullCpuTime');
      usort($special, 'CompareFullCpuTime');
      $assumed_min = 0.0;
      $sort_index = $DATA_TIME_SORT;
      $row_min = $time_min;

   } elseif ($Sort=='kb'){
      usort($succeeded, 'CompareMemoryUse');
      usort($special, 'CompareMemoryUse');
      $assumed_min = 256;
      $sort_index = DATA_MEMORY;
      if ($mem_min < 256){ $mem_min = 256; }
      settype($mem_min,'double');
      $row_min = $mem_min;

   } elseif ($Sort=='gz'){
      usort($succeeded, 'CompareGz');
      usort($special, 'CompareGz');
      $assumed_min = 128;
      $sort_index = DATA_GZ;
      $row_min = $gz_min;

   } elseif ($Sort=='elapsed'){
      usort($succeeded, 'CompareElapsed');
      usort($special, 'CompareElapsed');
      $assumed_min = 0.0;
      $sort_index = $DATA_TIME_SORT;
      $row_min = $time_min;
   }

   $labels = array();
   $ratios = array();
   $count = 0; $max = 15;
   foreach($succeeded as $row){
      $k = $row[DATA_LANG];
      if (isset($SLangs[$k])){
         $labels[] = $k;
         unset($SLangs[$k]);
         $row_value = $row[$sort_index];
         $ratios[] = $row_value > $assumed_min ? $row_value/$row_min : 1.0;
         $count++;
      }
      if ($count == $max){ break; }
   }

   return array($succeeded,$failed,$special,$labels,$ratios);
}


function CompareFullCpuTime($a, $b){
   return  $a[DATA_FULLCPU] < $b[DATA_FULLCPU] ? -1 : 1;
}

function CompareMemoryUse($a, $b){
   return  $a[DATA_MEMORY] < $b[DATA_MEMORY] ? -1 : 1;
}

function CompareGz($a, $b){
   return  $a[DATA_GZ] < $b[DATA_GZ] ? -1 : 1;
}

function CompareElapsed($a, $b){
   return  $a[DATA_ELAPSED] < $b[DATA_ELAPSED] ? -1 : 1;
}


// PAGE ////////////////////////////////////////////////

$Page = & new Template(LIB_PATH);
$Body = & new Template(LIB_PATH);
$PageId = 'benchmark';
$TemplateName = 'performance.tpl.php';


// GET_VARS ////////////////////////////////////////////////

list($Incl,$Excl) = WhiteListInEx();
$Tests = WhiteListUnique('test.csv',$Incl); // assume test.csv in name order
$Langs = WhiteListUnique('lang.csv',$Incl); // assume lang.csv in name order

$SLangs = SelectedLangs($Langs);

if (isset($_GET['test'])
      && strlen($_GET['test']) && (strlen($_GET['test']) <= NAME_LEN)){
   $X = $_GET['test'];
   if (ereg("^[a-z]+$",$X)){ $T = $X; }
}
$Available = isset($T) && isset($Tests[$T]) && isset($Incl[$T]);
if (!$Available){ $T = 'nbody'; }


if (isset($_GET['sort'])
      && strlen($_GET['sort']) && (strlen($_GET['sort']) <= 7)){
   $X = $_GET['sort'];
   if (ereg("^[a-z]+$",$X) && ($X == 'fullcpu' || $X == 'kb' || $X == 'gz' || $X == 'elapsed')){
      $S = $X;
   }
}
if (!isset($S)){
   $S = 'elapsed';
}


// HEADER ////////////////////////////////////////////////

$mark = MarkTime();
$mark = $mark.' '.SITE_NAME;

$TestName = $Tests[$T][TEST_NAME];
$Title = ($Available) ? $TestName.' benchmark' : 'Not Available';

$bannerUrl = CORE_SITE;
$faqUrl = CORE_SITE.'play.php';


// DATA ////////////////////////////////////////////////

$Data = BenchmarkData(DATA_PATH.'data.csv',$T,$Langs, $Incl,$Excl,$S,$SLangs);


// ABOUT ////////////////////////////////////////////////

$About = & new Template(ABOUT_PATH);
$AboutTemplateName = $T.SEPARATOR.'about.tpl.php';
if (! file_exists(ABOUT_PATH.$AboutTemplateName)){ $AboutTemplateName = 'blank-about.tpl.php'; }


// META ////////////////////////////////////////////////

$MetaKeywords = '<meta name="description" content="Compare programs that '.$Tests[$T][TEST_META].' for ~24 programming languages ('.PLATFORM_NAME.')." />';

// last field of test.csv has linefeed
$MetaKeywords = str_replace(array("\r", "\n"), '', $MetaKeywords);

$canonicalPage = FALSE;
if ($Available) {

   if (!isset($LinkRelCanonical)){
      $canonicalPage = SITE_NAME == 'u32';
      if ($canonicalPage) {
         $metaRobots = '<meta name="robots" content="index,nofollow,archive" />';
      } else {
         $LinkRelCanonical = '<link rel="canonical" href="http://python.milde.cz/u64q/performance.php?test='.$T.'" />';
         $metaRobots = '<meta name="robots" content="index,nofollow,noarchive" />';
      }
   }
} else {
   $metaRobots = '<meta name="robots" content="noindex,nofollow" />';
   unset($LinkRelCanonical);
}


// TEMPLATE VARS ////////////////////////////////////////////////

$Page->set('PageTitle', $Title.BAR.'Python&nbsp;Interpreters&nbsp;Benchmarks&nbsp;Game');
$Page->set('BannerTitle', BANNER_TITLE);
$Page->set('BannerUrl', $bannerUrl);
$Page->set('FaqTitle', FAQ_TITLE);
$Page->set('FaqUrl', $faqUrl);

$Body->set('Tests', $Tests);
$Body->set('SelectedTest', $T);
$Body->set('Langs', $Langs);
$Body->set('Excl', $Excl);
$Body->set('Mark', $mark);

$Body->set('Sort', $S);
$Body->set('Data', $Data );
$Body->set('Title', $Title);
$Body->set('CanonicalPage', $canonicalPage);

$About->set('SelectedTest', $T);
$Body->set('About', $About->fetch($AboutTemplateName));

$Page->set('PageBody', $Body->fetch($TemplateName));
$Page->set('Robots', $metaRobots);
$Page->set('MetaKeywords', $MetaKeywords);
if (isset($LinkRelCanonical)) { $Page->set('LinkCanonical', $LinkRelCanonical); }
$Page->set('PageId', $PageId);

echo $Page->fetch('page.tpl.php');
?>
