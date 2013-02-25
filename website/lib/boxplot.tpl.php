<?   // Copyright (c) Isaac Gouy 2009-2013 ?>

<?
   MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang);

   list($score,$labels,$stats,$selected) = $Data;
   unset($Data);  

   $pageUrl = CORE_SITE.SITE_NAME.'/which-programs-are-fastest.php';
   $chart = 'chartbox.php';
?>

<p><g:plusone annotation="none"></g:plusone></p>

<p><em>"What gets us into trouble is not what we don't know, it's what we know for sure that just ain't so."</em></p>

<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>

<h2><a href="<?=$pageUrl;?>#chart" name="chart">&nbsp;<strong>Which programs are fastest?</strong></a></h2>

<p>This <a href="http://mathworld.wolfram.com/Box-and-WhiskerPlot.html">box plot</a> shows <em>how many times slower</em>, the fastest benchmark <strong>programs</strong> for selected programming language implementations were, compared to the fastest <strong>programs</strong>.</p>

<p><img src="<?=$chart;?>?<?='s='.Encode($stats);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($labels);?>"
   alt=""
   title=""
   width="480" height="300"
 /></p>


<p>These are not the only programming languages. These are not the only compilers and interpreters. These are not the only programs that could be written. These are not the only tasks that could be solved. <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.php"><strong>These are just 10 tiny examples.</strong></a></p>

<p><em>How many times slower</em>, the fastest benchmark <em>programs</em> for programming selected language implementations were, compared to the fastest <em>programs</em>.</p>

<form method="get" action="which-programs-are-fastest.php">

<table>
<colgroup span="2" class="txt"></colgroup>
<colgroup span="7" class="num"></colgroup>
<tr class="score">
<td colspan="9" class="num">
<input type="submit" name="calc" value="chart" />
</td>
</tr>

<tr>
<th>&nbsp;</th>
<th>direct comparisons</th>
<th><a href="<?=$pageUrl;?>#about">&nbsp;|-</a></th>
<th><a href="<?=$pageUrl;?>#about">&nbsp;|---</a></th>
<th><a href="<?=$pageUrl;?>#about">&nbsp;25%</a></th>
<th><a href="<?=$pageUrl;?>#about">median</a></th>
<th><a href="<?=$pageUrl;?>#about">&nbsp;75%</a></th>
<th><a href="<?=$pageUrl;?>#about">&nbsp;---|</a></th>
<th><a href="<?=$pageUrl;?>#about">&nbsp;-|</a></th>
</tr>


<?
foreach($score as $k => $v){
   $Name = $Langs[$k][LANG_FULL];
   $HtmlName = $Langs[$k][LANG_HTML];

   $checked = '';
   if (isset($selected[$k])){ $checked = 'checked="checked"'; }

   printf('<tr>');
   printf('<td class="score"><p><input type="checkbox" name="%s" %s /></p></td>', $k, $checked); echo "\n";

   if (isset($Langs[$k][LANG_SPECIALURL]) && !empty($Langs[$k][LANG_SPECIALURL])){
      printf('<td><a href="%s.php" title="Compare %s program performance against other programs">%s</a></td>', $Langs[$k][LANG_SPECIALURL],$Name,$HtmlName); 
   } else {
      printf('<td><a href="compare.php?lang=%s" title="Compare %s program performance against other programs">%s</a></td>', $k,$Name,$HtmlName);
   }
   echo "\n";

   printf('<td>%0.2f</td><td>%0.2f</td><td>%0.2f</td><td class="sort">%0.2f</td><td>%0.2f</td><td>%0.2f</td><td>%0.2f</td>',
      $v[STAT_MIN], $v[STAT_XLOWER], $v[STAT_LOWER], $v[STAT_MEDIAN],
      $v[STAT_UPPER], $v[STAT_XUPPER], $v[STAT_MAX]); echo "\n";
   echo "</tr>\n";
}
?>

<tr class="score">
<td colspan="9" class="num">
<input type="submit" name="calc" value="chart" />
</td>
</tr>
</table>
</form>


<h3><a href="<?=$pageUrl;?>#about" name="about">&nbsp;</a></h3>
<?=$About;?>


