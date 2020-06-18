<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
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

<h1><a href="<?=$BannerUrl;?>" title="dual-core and one core, x86 and x64 measurements"><?=$BannerTitle;?></a></h1>

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
