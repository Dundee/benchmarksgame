<?   // Copyright (c) Isaac Gouy 2009-2012 ?>


<?    
   function CompareTime($b, $a){
      if ($a[1] == $b[1]) return 0;
      return  ($a[1] < $b[1]) ? -1 : 1;
   }
   
   list($Shapes,$Centers) = $Data;
   uasort($Centers,'CompareTime');
   $bounds = array(0.0,1.3,1.8,2.3,1000.0);
   define('NCOLS',sizeof($bounds)-1);

   /* Match the median time to a median time already assigned
      in cols - use the same row if possible.
   */

   function rowmatch($start,$stop,$k,$cols,$Centers){
      $rowmatch = $start;
      $y = $Centers[$k][1];
      for ($col=0; $col<NCOLS; $col++){
         for ($r=$start; $r<$stop; $r++){
            if (isset($cols[$col][$r])){
               $k0 = $cols[$col][$r];
               if ($k0 != $k){
                  $diff = abs($Centers[$k0][1] - $y);
                  if (!isset($match)||($diff < $match)){
                     $match = $diff; $rowmatch = $r;
                  }
               }
            }
         }
      }
      return $rowmatch;
   }

   /* Assign each language implementation to a source code size column
      based on arbitrary source code size.
   */

   function leftToRight($bounds,$Centers){
      $cols = array();
      for ($col=0;$col<NCOLS;$col++){
         $b0 = $bounds[$col]; $b1 = $bounds[$col+1];
         $i = 0;
         if ($col==0){
            foreach($Centers as $k => $c){
               if ($c[0] < $b1){ $cols[$col][$i] = $k; $i++; }
            }
            $n = $i;
         } else {
            foreach($Centers as $k => $c){
               if ($c[0] >= $b0 && $c[0] < $b1){
                  $r = rowmatch($i,$n,$k,$cols,$Centers);
                  $cols[$col][$r] = $k;
                  $i = $r + 1;
               }
            }
            $n = ($n > $i) ? $n : $i;
         }
      }
      return array($n,$cols);
   }

   function finetune($n,$cols0,$Centers){
      $colrow = array();
      foreach($cols0 as $c => $a){
         foreach($a as $r => $k){
            $colrow[$k] = array($c,$r);
         }
      }

      $cols = array();
      $edge = sizeof($cols0)-1;
      $keys = array_keys(array_reverse($Centers));

      foreach($keys as $k){
         $cr = $colrow[$k];
         $col = $cr[0];
         if ($col==$edge){
            $cols[$col][ $cr[1] ] = $k;
         } else {
            $r = rowmatch($cr[1],$n,$k,$cols0,$Centers);
            while ($r > $cr[1] && isset($cols[$col][$r])){ $r--; }
            $cols[$col][$r] = $k;
         }
      }

      // we might have emptied some row - get rid of empty rows

      for ($r=0; $r<$n; $r++){
         $before[$r] = $r;
      }
      $after = array();
      foreach($cols as $c => $a){
         foreach($a as $r => $k){
            $after[$r] = $r;
         }
      }
      $empty = array_keys( array_diff($before,$after));
      ksort($empty);

      if (sizeof($empty)>0){
         foreach($cols as $c => $a){
            $flip = array_flip($a);
            foreach($empty as $e){
               foreach($flip as $k => $v){
                  if ($v > $e){
                     $flip[$k] = $v-1;
                  }
               }
            }
            $shifted[$c] = array_flip($flip);
         }
      } else {
         $shifted = $cols;
      }

      return array($n-sizeof($empty),$shifted);
   }


   list($n,$cols) = leftToRight($bounds,$Centers);
   list($n,$cols) = finetune($n,$cols,$Centers);
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

<h2><a href="#shapes" name="shapes">&nbsp;<?=$Title;?>&nbsp;[<?=$Mark;?>]</a></h2>
<p>From <em>more-concise at page left</em> to less-concise at the right, from slower at page top to <em>faster at the bottom</em>.</p>

<p>These scatter plots show the fastest programs contributed for each programming language implementation, measured on this computer -- so they don't show <a href="#shortest" title="Shortest C++ programs">&darr;&nbsp;slower more-concise programs</a> that still seem relatively fast.</p>

<p>These are not the only programs that could be written. These are not the only programming languages. These are not the only compilers and interpreters. These are not the only tasks that could be solved. <a href="<?=CORE_SITE;?>dont-jump-to-conclusions.php"><strong>These are just 10 tiny examples.</strong></a></p>


<?
   printf('<table>');

   for ($row=0; $row<$n; $row++){
      printf('<tr>');
      for ($col=0; $col<NCOLS; $col++){  
         printf('<td>&nbsp;');
         if (isset($cols[$col][$row])){
            $k = $cols[$col][$row];


            // hard-code a shortest programs example
            if ($k=='shortest'){
               printf('<a href="#shortest" name="shortest"><img src="chartshape.php?w=%s&amp;s=%s&amp;c=%s"  alt="Shortest C++ programs" title="Shortest C++ programs" width="150" height="120" /></a>',
                  Encode($k), Encode($Shapes[$k]), Encode($Centers[$k]));
            } else {


               if (isset($Langs[$k][LANG_SPECIALURL]) && !empty($Langs[$k][LANG_SPECIALURL])){
                  printf('<a href="%s.php" title="Is %s the fastest programming language?">', $Langs[$k][LANG_SPECIALURL], $Langs[$k][LANG_FULL]);
               } else {
                  printf('<a href="compare.php?lang=%s" title="Is %s the fastest programming language?">', $k, $Langs[$k][LANG_FULL]);
               }

               printf('<img src="chartshape.php?w=%s&amp;s=%s&amp;c=%s" alt="source code size versus speed of %s benchmark programs" title="Compare %s program size and speed against other programs" width="150" height="120" />',
                  Encode($k), Encode($Shapes[$k]), Encode($Centers[$k]), $Langs[$k][LANG_FULL], $Langs[$k][LANG_FULL] );

               printf('</a>');
            }

         } else {
            printf('&nbsp;');
         }
         printf('</td>');
      }
      printf('</tr>');
   }
   printf('</table>');
?>

<h3><a href="#about" name="about">&nbsp;about <?=$Title;?></a></h3>
<?=$About;?>

