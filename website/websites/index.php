<?php
ob_start('ob_gzhandler');
$s = time();
$baseUrl = 'https://pybenchmarks.org';

// REVISED - don't have all pages expire at the same time!
// EXPIRE pages 31 hours after they are visited.
header("Pragma: public");
header("Cache-Control: maxage=".(1*3600).",public");
header("Expires: " . gmdate("D, d M Y H:i:s", $s + (1*3600)) . " GMT");
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />

<meta name="robots" content="index,follow,archive" /><meta name="revisit" content="14 days" />

<meta name="description" content="Compare the time and memory use of programs written in ~24 programming languages to solve ~12 simple benchmark tasks. Contribute your own programs." />

<meta name="HandheldFriendly" content="false" />
<meta name="google-site-verification" content="y9GFMJuxj7Ou4xK9YRagz9hCBfn1lyKcHQakWgkE7gg" />

<title>Python Interpreters Benchmarks</title>
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl ?>/benchmark_css_8oct2012.php" />
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl ?>/nohint_css_26jan2011.php" media="screen,print,projection"/>
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl ?>/hint_css_26jan2011.php" media="handheld,aural,braille"/>
<link rel="shortcut icon" href="<?php echo $baseUrl ?>/favicon_ico_11dec2009.php" />
</head>

<body id="core">

<table class="banner"><tr>
<td><h1><a><strong>Python</strong>&nbsp;Interpreters <strong>Benchmarks</strong></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $baseUrl ?>/play.php" title="How programs were measured. FAQs. How to contribute programs.">[[ Play ]]</a></h1></td>
</tr></table>

<div id="sitemap">

<p>
Benchmarks of Python interpreters and compilers.
</p>

<p>
Benchmarks are only tentative.
Feel free to <a href="https://github.com/Dundee/benchmarksgame">contribute</a> if you know how to improve the test programs.
</p>


<table>
<?php
$sites = array('u64q');

function PrintHeaders(){
   echo '<tr><th>&nbsp;</th><th></th><th></th><th></th></tr>';
   echo '<tr>';
   echo '<th class="u64q">&nbsp;x64&nbsp;Arch Linux&#8482; Intel&#174;&nbsp;i5-7200U&#174; dual-core&nbsp;</th>';
   echo '</tr>';
   echo '<tr><th>&nbsp;</th><th></th><th></th><th></th></tr>';
}


PrintHeaders();

$allsites = array('u64q');

$langs = array(
   array('python','Python 2.7.15 (with NumPy and gmpy)','python',$allsites),
   array('pypy','PyPy 6.0.0 (with gmpy_cffi)','pypy',$allsites),
   array('python3','Python 3.7.1 (with NumPy and gmpy2)','python3',$allsites),
   array('python-dev','Python 3.8.0a0.r102209','python-dev',$allsites),
   array('pypy3','PyPy3 6.0.0 (with gmpy_cffi)','pypy3',$allsites),
   array('ipy','IronPython 2.7.8a1 (with Mono 5.0.0.100)','ipy',$allsites),
   array('jython','Jython 2.7.1 (with JDK 8.u131)','jython',$allsites),
   array('cython','Cython 0.29.4 (with NumPy and gmpy)','cython',$allsites),
   array('nuitka','Nuitka 0.6.0','nuitka',$allsites),
   array('shedskin','Shedskin 0.9.4','shedskin',$allsites),
   array('numba','Numba 0.35.0 (with NumPy and Anaconda 5.0.1)','numba',$allsites),
   array('pyston','Pyston 0.6.1','pyston',$allsites),
   array('micropython','MicroPython 1.10','micropython',$allsites),
   array('grumpy','Grumpy r123.08f9c2e','grumpy',$allsites),
   array('graal','Graal python 1.0.0_rc11','graal',$allsites),
   );


$tag = array(
   'u64q' =>'on multi core 64 bit Linux'
   );

foreach($langs as $lang){
   printf('<tr>');
   $name = $lang[1];
   $langsites = $lang[3];
   foreach($sites as $s){
      if (in_array($s,$langsites)){
         if (!empty($lang[2])){
            printf('<td><a href="' . $baseUrl . '/%s/%s.php" title="Compare %s program performance %s">%s</a></td>', $s, $lang[2], $name, $tag[$s], $name );
         } else {
            printf('<td><a href="' . $baseUrl . '/%s/compare.php?lang=%s" title="Compare %s program performance %s">%s</a></td>', $s, $lang[0], $name, $tag[$s], $name );
         }
      }
      else {
         printf('<td>&nbsp;</td>');
      }
   }
   printf('</tr>');
}


PrintHeaders();

$tests = array(
   array('nbody','n-body','Perform an N-body simulation of the Jovian planets')
   ,array('fannkuchredux','fannkuch-redux','Repeatedly access a tiny integer-sequence')
   ,array('meteor','meteor-contest','Search for solutions to shape packing puzzle')
   ,array('fasta','fasta','Generate and write random DNA sequences')
   ,array('fibonacci', 'fibonacci', 'Calculate Fibonacci number')
   ,array('fib50', 'fib50', 'Calculate 50th Fibonacci number')
   ,array('spectralnorm','spectral-norm','Calculate an eigenvalue using the power method')
   ,array('revcomp','reverse-complement','Read DNA sequences and write their reverse-complement')
   ,array('mandelbrot','mandelbrot','Generate a Mandelbrot set and write a portable bitmap')
   ,array('knucleotide','k-nucleotide','Repeatedly update hashtables and k-nucleotide strings')
   ,array('regexdna','regex-dna','Match DNA 8-mers and substitute nucleotides for IUB code')
   ,array('pidigits','pidigits','Calculate the digits of Pi with streaming arbitrary-precision arithmetic')
   ,array('chameneosredux','chameneos-redux','Repeatedly perform symmetrical thread rendezvous requests')
   ,array('threadring','thread-ring','Repeatedly switch from thread to thread passing one token')
   ,array('templates','templates','Render Jinja2 template')
   ,array('binarytrees','binary-trees','Allocate and deallocate many many binary trees')
   ,array('pystone','pystone','PyStone benchmark')
   ,array('richards','richards','Richards benchmark')
   ,array('iobench','iobench','Input/output benchmark')
   ,array('jsonbench','jsonbench','JSON encoding/decoding benchmark')
   );

foreach($tests as $t){
   printf('<tr>');
   foreach($sites as $s){
      if ($s=='u64q'){
         printf('<td><a href="' . $baseUrl . '/%s/performance.php?test=%s">%s</a></td>', $s, $t[0], $t[2] );
      } else {
         printf('<td><a href="' . $baseUrl . '/%s/performance.php?test=%s" title="%s">%s</a></td>', $s, $t[0], $t[2], $t[1] );
      }
   }
   printf('</tr>');
}

PrintHeaders();

$page = array(
    array('which-programs-are-fastest.php','Which programs are fastest?','Which of these interpreters have the fastest benchmark programs?')
   );

foreach($page as $p){
   printf('<tr>');
   foreach($sites as $s){
      printf('<td><a href="' . $baseUrl . '/%s/%s" title="%s">%s</a></td>', $s, $p[0], $p[2], $p[1] );
   }
   echo "</tr>";
}

?>

</table>


<p class="imgfooter">
   &nbsp; <a href="<?php echo $baseUrl ?>/dont-jump-to-conclusions.php">Conclusions</a>
   &nbsp; <a href="<?php echo $baseUrl ?>/license.php">License</a>
   &nbsp; <a href="<?php echo $baseUrl ?>/play.php">Play</a>
   &nbsp; <a href="https://github.com/Dundee/benchmarksgame">Source code</a>
   &nbsp;
</p>

</div>


<?php include_once("analyticstracking.php") ?>
</body>
</html>
