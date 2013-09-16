<?
header("Content-type: image/png");

// Copyright (c) Isaac Gouy 2009-2013

// LIBRARIES ////////////////////////////////////////////////

require_once(LIB_PATH.'lib_chart.php');


// DATA ////////////////////////////////////////////////////

$d = array(
      array(
  array(2006,0,'gp4',0.00)
 ,array(2007,1,'gp4',0.01)
 ,array(2007,2,'gp4',0.01)
 ,array(2007,3,'gp4',0.02)
 ,array(2007,4,'gp4',0.02)
 ,array(2007,5,'gp4',0.03)
 ,array(2007,6,'gp4',0.12)
 ,array(2007,7,'gp4',0.12)
 ,array(2007,8,'gp4',0.15)
 ,array(2007,9,'gp4',0.16)
 ,array(2007,10,'gp4',0.17)
 ,array(2007,11,'gp4',0.19)
 ,array(2007,12,'gp4',0.21)
 ,array(2008,13,'gp4',0.27)
 ,array(2008,14,'gp4',0.31)
 ,array(2008,15,'gp4',0.40)
 ,array(2008,16,'gp4',0.55)
 ,array(2008,17,'gp4',0.73)
 ,array(2008,18,'gp4',0.79)
 ,array(2008,19,'gp4',1.00)
       )
       ,array(
  array(2006,0,'debian',0.00)
 ,array(2007,1,'debian',0.14)
 ,array(2007,2,'debian',0.15)
 ,array(2007,3,'debian',0.16)
 ,array(2007,4,'debian',0.24)
 ,array(2007,5,'debian',0.26)
 ,array(2007,6,'debian',0.29)
 ,array(2007,7,'debian',0.29)
 ,array(2007,8,'debian',0.29)
 ,array(2007,9,'debian',0.29)
 ,array(2007,10,'debian',0.31)
 ,array(2007,11,'debian',0.38)
 ,array(2007,12,'debian',0.89)
 ,array(2008,13,'debian',0.89)
 ,array(2008,14,'debian',1.00)
        )
       ,array(
  array(2012,68,'u32',0.00)
 ,array(2012,69,'u32',0.01)
 ,array(2012,70,'u32',0.01)
 ,array(2012,71,'u32',0.01)
 ,array(2012,72,'u32',0.01)
 ,array(2013,73,'u32',0.18)
 ,array(2013,74,'u32',0.22)
 ,array(2013,75,'u32',0.25)
 ,array(2013,76,'u32',0.46)
 ,array(2013,77,'u32',0.56)
 ,array(2013,78,'u32',0.66)
 ,array(2013,79,'u32',0.73)
 ,array(2013,80,'u32',0.85)
 ,array(2013,81,'u32',1.00)
       ) );
       


// CHART /////////////////////////////////////////////////////

$chart = new StepChart();

$chart->yscale = 1.7;
$chart->yAxis(axisPercent(axis10()));
$chart->xAxis(axisYrMth(),3.9);

$chart->steps(GP4,$d[0]);
$chart->steps(DEBIAN,$d[1]);
$chart->steps(U32,$d[2]);

$chart->title('Cumulative Percentage of Measurements by Month');
$chart->xAxisLegend('month');
$chart->yAxisLegend('cumulative percentage');
$chart->frame();
$chart->complete();

?>
