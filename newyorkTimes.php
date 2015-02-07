<?php
include "lib.php";	
$url = "http://www.nytimes.com/".$_GET['path'];
echo fetch($url); 
?>