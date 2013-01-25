<?php

// REVISED - don't have all pages expire at the same time!
// EXPIRE pages 31 hours after they are visited.
$s = time();
header("Pragma: public");
header("Cache-Control: maxage=".(31*3600).",public");
header("Expires: " . gmdate("D, d M Y H:i:s", $s + (31*3600)) . " GMT");
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<?=$Robots;?>
<?=$MetaKeywords;?>
<meta name="HandheldFriendly" content="false" />

<title><?=$PageTitle;?></title>
<link rel="stylesheet" type="text/css" href="http://benchmarksgame.alioth.debian.org/benchmark_css_8oct2012.php" />
<link rel="stylesheet" type="text/css" href="http://benchmarksgame.alioth.debian.org/nohint_css_26jan2011.php" media="screen,print,projection"/>
<link rel="stylesheet" type="text/css" href="http://benchmarksgame.alioth.debian.org/hint_css_26jan2011.php" media="handheld,aural,braille"/>
<link rel="shortcut icon" href="http://benchmarksgame.alioth.debian.org/favicon_ico_11dec2009.php" />
<?=$LinkCanonical;?>

</head>
<body id="<?=SITE_NAME;?>">
<p id="hint"><a href="http://benchmarksgame.alioth.debian.org/mobile/index.php">/mobile Handheld Friendly website</a></p>


<?
   if ($PageId == 'faq') { 
      $FaqRollover = " Apples and Oranges. Programmer skill and effort. A good starting point."; 
   } else { 
      $FaqRollover = "How programs were measured. FAQs. How to contribute programs."; 
   }
?>

<table class="banner"><tr>
<td><h1><a href="<?=$BannerUrl;?>" title="Computer Language Benchmarks Game Home"><?=$BannerTitle;?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?=$FaqUrl;?>" title="<?=$FaqRollover;?>"><?=$FaqTitle;?></a></h1></td>
</tr></table>

<div id="<?=$PageId;?>">
<?=$PageBody;?>

<p class="imgfooter">
<a href="<?=CORE_SITE;?>license.php" title="The Computer Language Benchmarks Game is published under this revised BSD license" >
   <img src="<?=IMAGE_PATH;?>open_source_button_png_11dec2009.php" alt="Revised BSD license" height="31" width="88" />
</a>
</p>
<p class="imgfooter">&nbsp; <a href="<?=CORE_SITE;?>">Home</a> &nbsp; <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.php">Conclusions</a> &nbsp; <a href="<?=CORE_SITE;?>license.php">License</a> &nbsp; <a href="<?=CORE_SITE;?>more.php">Play</a> &nbsp;</p>
</div>


<? include_once(IMAGE_PATH.'analyticstracking.php'); ?>
</body>
</html>
