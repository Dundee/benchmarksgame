<?php

function fibonacci($n){
	if ($n == 0)
	return 0;

	if($n == 1 || $n == 2)
	return 1;

	$int1 = '1';
	$int2 = '1';

	$fib = '0';

	//start from n==3
	for($i=1; $i<=$n-2; $i++ )
	{
	$fib = bcadd($int1, $int2);
	//swap the values out:
	$int2 = $int1;
	$int1 = $fib;
	}

	return $fib;
}

echo fibonacci($_SERVER['argv'][1]);
echo "\n";
