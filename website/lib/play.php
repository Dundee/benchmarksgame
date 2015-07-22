<?php
// Copyright (c) Isaac Gouy 2004-2013

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_common.php');
require_once(LIB);


// TEMPLATE VARS ////////////////////////////////////////////////

$Page = & new Template(LIB_PATH);
$Page->set('PageTitle', 'Play'.BAR.'Python&nbsp;Interpreters&nbsp;Benchmarks&nbsp;Game');
$Page->set('BannerTitle', 'Python&nbsp;Interpreters&nbsp; <br/>Benchmarks&nbsp;Game');
$Page->set('FaqTitle', '[[ Conclusions ]]');
$Page->set('PageBody', BLANK);

$Body = & new Template(LIB_PATH);
$Body->set('Download', DOWNLOAD_PATH);
$Body->set('Changed', filemtime(LIB_PATH.'play.tpl.php'));

$Page->set('PageBody', $Body->fetch('play.tpl.php'));


$metaRobots = '<meta name="robots" content="index,follow,archive" /><meta name="revisit" content="10 days" />';
$bannerUrl = CORE_SITE;

$faqUrl = CORE_SITE.'dont-jump-to-conclusions.php';
$MetaKeywords = '<meta name="description" content="How programs were measured. FAQs. How to contribute programs. " />';

$Page->set('Robots', $metaRobots);
$Page->set('BannerUrl', $bannerUrl);
$Page->set('FaqUrl', $faqUrl);
$Page->set('MetaKeywords', $MetaKeywords);
$Page->set('PageId', 'faq');

echo $Page->fetch('page.tpl.php');
?>

