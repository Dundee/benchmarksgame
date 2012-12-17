<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2004-2012

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_whitelist.php');
require_once(LIB_PATH.'lib_chart.php');

SetChartCacheControl();

$in = WhiteListIn();
$WhiteListLangs = WhiteListUnique('lang.csv',$in);

// DATA ////////////////////////////////////////////////////

list ($Mark,$valid) = ValidMark(TRUE);
list ($BackText,$valid) = ValidLangs($WhiteListLangs,$valid);
list ($Values,$valid) = ValidMatrix('g',1,$valid);
//for ($i=0;$i<sizeof($Values);$i++) $Values[$i] = log10($Values[$i]);


// CHART /////////////////////////////////////////////////////

$chart = new WideBarChart();
$chart->yAxis(axis10());

if ($valid){
   $chart->backgroundText($BackText);
   $chart->bars($Values);
   $chart->notice($Mark);
}

$chart->xAxisLegend('selected language implementations');
$chart->yAxisLegend('Weighted Geometric Mean - smaller is better');
$chart->title('Ten tiny examples - How many times more time/memory/code used?');
$chart->frame();
$chart->complete();

?>
