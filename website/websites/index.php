<?php
ob_start('ob_gzhandler');
$s = time();
$baseUrl = 'https://pybenchmarks.org';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="robots" content="index,follow,archive" /><meta name="revisit" content="14 days" />
<meta name="description" content="Compare the time and memory use of benchmarks runned by ~15 Python interpreters/compilers." />
<meta name="google-site-verification" content="y9GFMJuxj7Ou4xK9YRagz9hCBfn1lyKcHQakWgkE7gg" />

<title>Python Interpreters Benchmarks</title>
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl ?>/benchmark.css" />
<link rel="shortcut icon" href="<?php echo $baseUrl ?>/favicon.ico" />
</head>

<body id="core">

<h1><a><strong>Python</strong>&nbsp;Interpreters <strong>Benchmarks</strong></a></h1>

<div id="sitemap">

<p>
Benchmarks of Python interpreters and compilers.
</p>

<p>
Benchmarks are only tentative.
Feel free to <a href="https://github.com/Dundee/benchmarksgame">contribute</a> if you know how to improve the test programs.
</p>

<div class="row">

<div class="left">

<h2>Interpreters and compilers</h2>

<table>
<?php
$sites = array('u64q');

$allsites = array('u64q');

$langs = array(
   array('python3','Python 3.9.0 (with NumPy and gmpy2)','python3',$allsites),
   array('python','Python 2.7.15 (with NumPy and gmpy)','python',$allsites),
   array('python-dev','Python 3.11.0a4','python-dev',$allsites),
   array('pypy3','PyPy3 7.3.7 (with gmpy_cffi)','pypy3',$allsites),
   array('pypy','PyPy 7.3.6 (with gmpy_cffi)','pypy',$allsites),
   array('ipy','IronPython 2.7.8a1 (with Mono 5.0.0.100)','ipy',$allsites),
   array('jython','Jython 2.7.1 (with JDK 8.u131)','jython',$allsites),
   array('cython','Cython 0.29.26 (with NumPy and gmpy)','cython',$allsites),
   array('nuitka','Nuitka 0.6.19.3','nuitka',$allsites),
   array('shedskin','Shedskin 0.9.4','shedskin',$allsites),
   array('numba','Numba 0.55.0rc1 (with NumPy)','numba',$allsites),
   array('pyston','Pyston 2.3','pyston',$allsites),
   array('micropython','MicroPython 1.11','micropython',$allsites),
   array('grumpy','Grumpy r123.08f9c2e','grumpy',$allsites),
   array('graal','Graal python 21.2.0','graal',$allsites),
   array('rustpython','RustPython 0.1.1','rustpython',$allsites),
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

?>

</table>
</div>

<div class="right">

<h2>Benchmarks</h2>
<table>

<?php

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

?>

</table>
</div>
</div>

<?php

$page = array(
    array('which-programs-are-fastest.php','Which interpreters/compilers are fastest?','Which of these interpreters have the fastest benchmark programs?')
   );

foreach($page as $p){
   foreach($sites as $s){
      printf('<strong><a href="' . $baseUrl . '/%s/%s" title="%s">%s</a></strong>', $s, $p[0], $p[2], $p[1] );
   }
}

?>

<p class="imgfooter">
   &nbsp; <a href="<?php echo $baseUrl ?>/dont-jump-to-conclusions.php">Conclusions</a>
   &nbsp; <a href="<?php echo $baseUrl ?>/license.php">License</a>
   &nbsp; <a href="<?php echo $baseUrl ?>/play.php">Play</a>
   &nbsp; <a href="https://github.com/Dundee/benchmarksgame">Source code</a>
   &nbsp;
</p>


<?php include_once("analyticstracking.php") ?>
</body>
</html>
