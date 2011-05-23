<?php
die();
include("MOBIClass/MOBI.php");

$optional = array(
	"title"=>"Title",
	"author"=>"Author"
);

$mobi = new MOBI();
$mobi->setInternetSource("INSERT URL HERE");
$mobi->setOptions($optional);

$mobi->download("sample.mobi");
?>