<?php
if(isset($_GET["download"])){
	//Only need to include the MOBI file, all other files are included automatically
	include("MOBIClass/MOBI.php");

	//Set the url (note that this file isn't created for eBook viewing, it's just
	//to demonstrate that you can give (almost) any page and get it working on
	//your eBook reader)
	$url = "http://thetricky.net/mySQL/GROUP%20BY%20vs%20ORDER%20BY";
	$recognize = false;

	//Create the mobi object
	$mobi = new MOBI();

	//Find and set the content handler
	$content = null;

	if($recognize){				//Pass through to right handler
		$content = RecognizeURL::GetContentHandler($url);
	}

	if($content == null){		//If handler not found or if the recognition was turned off
		$content = new OnlineArticle($url);
	}

	$mobi->setContentProvider($content);

	//Get title and make it a 12 character long filename
	$title = $mobi->getTitle();
	if($title === false) $title = "file";
	$title = urlencode(str_replace(" ", "_", strtolower(substr($title, 0, 12))));

	//Send the mobi file as download
	$mobi->download($title.".mobi");
	die;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Sample</title>
	</head>
	<body>
		<a href='index.php?download'>Download</a>
	</body>
</html>
