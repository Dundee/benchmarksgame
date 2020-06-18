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
<link rel="stylesheet" type="text/css" href="https://pybenchmarks.org/benchmark.css" />
<link rel="shortcut icon" href="https://pybenchmarks.org/favicon.ico" />
<?php
   if (isset($LinkCanonical)) { echo $LinkCanonical; }
?>

</head>
<body id="<?=SITE_NAME;?>">


<?php
   if ($PageId == 'faq') {
      $FaqRollover = " Apples and Oranges. Programmer skill and effort. A good starting point.";
   } else {
      $FaqRollover = "How programs were measured. FAQs. How to contribute programs.";
   }
?>

<table class="banner"><tr>
<td><h1><a href="<?=$BannerUrl;?>" title="dual-core and one core, x86 and x64 measurements"><?=$BannerTitle;?></a></h1></td>
</tr></table>

<div id="<?=$PageId;?>">
<?=$PageBody;?>

<p class="imgfooter">
<a href="<?=CORE_SITE;?>license.php" title="Python Interpreters Benchmarks is published under this revised BSD license" >
   <img src="<?=IMAGE_PATH;?>open_source_button_png_11dec2009.php" alt="Revised BSD license" height="31" width="88" />
</a>
</p>
<p class="imgfooter">&nbsp; <a href="<?=CORE_SITE;?>">Home</a> &nbsp; <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.php">Conclusions</a> &nbsp; <a href="<?=CORE_SITE;?>license.php">License</a> &nbsp; <a href="<?=CORE_SITE;?>play.php">Play</a> &nbsp;</p>
</div>


<?php include_once(IMAGE_PATH.'analyticstracking.php'); ?>
</body>
</html>
