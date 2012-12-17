<?   // Copyright (c) Isaac Gouy 2004-2012 ?>

<?
MkMenuForm($Tests,$SelectedTest,$Langs,$SelectedLang);
?>

<?
   list($score,$labels,$ratio,$selected) = $Data;
   unset($Data);
?>

<p><em>"What gets us into trouble is not what we don't know, it's what we know for sure that just ain't so."</em></p>

<h2><a href="#chart" name="chart">&nbsp;How big is the difference between programs?</a></h2>
<p>Selected and weighted 'how-many-times-more compared to the-program-that-used-least' scores are compressed into one number, the <a href="http://mathworld.wolfram.com/WeightedMean.html">weighted</a> <a href="http://mathworld.wolfram.com/GeometricMean.html">geometric mean</a>, at the risk of being <i>"neat, plausible, and wrong"</i>.</p>

<p><img src="chartscore.php?<?='g='.Encode($ratio);?>&amp;<?='m='.Encode($Mark);?>&amp;<?='w='.Encode($labels);?>"
   width="480" height="300" alt=""
 /></p>

<p>These are not the only programming languages. These are not the only compilers and interpreters. These are not the only programs that could be written. These are not the only tasks that could be solved. <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.php"><strong>These are just 10 tiny examples.</strong></a></p>



<form method="get" action="which-programs-are-best.php">


<table class="layout">
<tr class="score"><td colspan="2" class="num">
<input type="submit" name="calc" value="chart" />
</td></tr>


<tr><td>

<table>
<colgroup span="2" class="txt"></colgroup>
<colgroup span="1" class="num"></colgroup>
<colgroup span="1" class="fun"></colgroup>
<tr>
<th>&nbsp;</th>
<th>compare&nbsp;2</th>
<th><a href="#about">GM</a></th>
<th><a href="#about">missing</a></th>
</tr>

<?
foreach($score as $k => $v){
   $HtmlName = $Langs[$k][LANG_HTML];

   $checked = '';
   if (isset($selected[$k])){ $checked = 'checked="checked"'; }

   printf('<tr>');
   printf('<td class="score"><p><input type="checkbox" name="%s" %s /></p></td>', $k, $checked);

   if (isset($Langs[$k][LANG_SPECIALURL]) && !empty($Langs[$k][LANG_SPECIALURL])){
      printf('<td><a href="%s.php" title="Compare %s program speed and size against other programs">%s</a></td>', $Langs[$k][LANG_SPECIALURL],$Langs[$k][LANG_FULL],$HtmlName); 
   } else {
      printf('<td><a href="compare.php?lang=%s" title="Compare %s program speed and size against other programs">%s</a></td>', $k,$Langs[$k][LANG_FULL],$HtmlName);
   }
   echo "\n";

   printf('<td>%0.2f</td><td>%s</td>',
      $v[SCORE_MEAN], PBlank($v[SCORE_TESTS])); echo "\n";
   echo "</tr>\n";
}
?>
</table>

</td><td>


<table>
<colgroup span="2" class="txt"></colgroup>

<tr><th>measure</th><th>weight</th></tr>
<tr>
<td><a href="<?=CORE_SITE;?>help.php#time">Time&nbsp;secs</a></td>
<td><input type="text" size="2" name="xfullcpu" value="<?=$W['xfullcpu'];?>" /></td>
</tr>
<tr>
<td><a href="<?=CORE_SITE;?>help.php#memory">Memory&nbsp;KB</a></td>
<td><input type="text" size="2" name="xmem" value="<?=$W['xmem'];?>" /></td>
</tr>
<tr>
<td><a href="<?=CORE_SITE;?>help.php#gzbytes">Code B</a></td>
<td><input type="text" size="2" name="xloc" value="<?=$W['xloc'];?>" /></td>
</tr>

<tr><th>benchmark</th><th>weight</th></tr>
<?
foreach($Tests as $t){
   $Link = $t[TEST_LINK];
   $Name = $t[TEST_NAME];
   $weight = $W[ $t[TEST_LINK] ];

   printf('<tr>'); echo "\n";
   printf('<td><a href="performance.php?test=%s" title="Measurements for all the %s benchmark programs">%s</a></td>', $Link,$Name,$Name); echo "\n";
   printf('<td><p><input type="text" size="2" name="%s" value="%d" /></p></td>', $Link, $weight); echo "\n";
   echo "</tr>\n";
}
?>

</table>

</td></tr>
<tr class="score">
<td colspan="2" class="num">
<input type="submit" name="calc" value="chart" />
</td>
</tr>
</table>
</form>


<h3><a href="#about" name="about">&nbsp;<strong><?=$Title;?></strong> <i>The Weighted Geometric Mean</i></a></h3>
<?=$About;?>
