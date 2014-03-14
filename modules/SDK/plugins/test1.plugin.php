<?php

add_action('test','add_test_code');
function add_test_code()
{
	echo "<p><p>Ciao Test CODE!!!</p></p>"; 
}


add_action('test1','add_test_code1');
function add_test_code1($saluto,$nome)
{
	echo "<p><p>$saluto Test1 CODE da $nome !!!</p></p>"; 
}


add_action('filter','add_filter_code');
function add_filter_code($url_arr)
{
	$val1 = $url_arr[2];
	return array($val1 	,'pippo');
}

?>