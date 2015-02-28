<?php   // Copyright (c) Isaac Gouy 2009-2014 ?>

<?php
   MkMenuForm($Tests,NULL,$Langs,NULL);

   list($labels,$stats) = $Data;
   unset($Data);

   // make 2 charts -- middle data & first+last data

   $n = sizeof($stats);
   $labels1 = array();
   $stats1 = array();

   for ($i=0; $i<$n; $i++){
      $labels1[] = $labels[$i];
      $stats1[] = $stats[$i];
   }

   $pageUrl = CORE_SITE.SITE_NAME.'/which-programs-are-fastest.php';
   $chart = 'chartbox.php';
?>

<p><em>"What gets us into trouble is not what we don't know, it's what we know for sure that just ain't so."</em></p>

<?php if ($CanonicalPage){ echo '<p><g:plusone annotation="none"></g:plusone></p>
<script type="text/javascript">
  (function() {
    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
    po.src = "https://apis.google.com/js/plusone.js";
    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
  })();
</script>'; } ?>

<h2><a href="<?=$pageUrl;?>#chart" name="chart">&nbsp;<strong>Which programs are fastest?</strong></a></h2>

<p>Please don't use this summary page to compare 2 programming language implementations -- <b>use the direct comparison</b>, for example <a href="python.php" title="Are the Python programs faster than the PyPy programs?">-all&nbsp;benchmarks- Python 2&nbsp;PyPy</a>.</p>


<p>This box plot shows <em>how many times slower</em>, the fastest benchmark programs for programming language implementations were, compared to the fastest programs written in <em>any of the programming languages</em>.</p>


<p><img src="<?=$chart;?>?<?='s='.Encode($stats1);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($labels1);?>"
   alt=""
   title=""
   width="480" height="300"
 /></p>

<p>Please don't obsess about which programming language implementation is shown 10<sup>th</sup> and which is shown 11<sup>th</sup>. You can see that the order would be different if it was based on the median scores instead of <a href="http://portal.acm.org/citation.cfm?id=5666.5673" title="How not to lie with statistics: the correct way to summarize benchmark results">the geometric mean</a> scores.</p>


<h3><a href="<?=$pageUrl;?>#about" name="about">&nbsp;</a></h3>
<?=$About;?>


