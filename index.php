<?php
$urlDemo = false;
if(isset($_GET["download"])){
	//Only need to include the MOBI file, all other files are included automatically
	include("MOBIClass/MOBI.php");

	if($urlDemo){
		//Set the url (note that this file isn't created for eBook viewing, it's just
		//to demonstrate that you can give (almost) any news article and get it working on
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
	}else{
		//Create the mobi object
		$mobi = new MOBI();
		
		$content = new MOBIFile();
		
		$content->set("title", "My first eBook");
		$content->set("author", "Me");
		
		$content->appendChapterTitle("Introduction");
		for($i = 0, $lenI = rand(5, 10); $i < $lenI; $i++){
			$content->appendParagraph("P".($i+1));
		}
		
		
		//Based on PHP's imagecreatetruecolor help paage
		$im = imagecreatetruecolor(220, 200);
		$text_color = imagecolorallocate($im, 233, 14, 91);
		imagestring($im, 10, 5, 5,  'A Simple Text String', $text_color);
		imagestring($im, 5, 15, 75,  'A Simple Text String', $text_color);
		imagestring($im, 3, 25, 125,  'A Simple Text String', $text_color);
		imagestring($im, 2, 10, 155,  'A Simple Text String', $text_color);
		$content->appendImage($im);
		imagedestroy($im);
		
		$content->appendPageBreak();
		
		for($i = 0, $lenI = rand(10, 15); $i < $lenI; $i++){
			$content->appendChapterTitle(($i+1).". Chapter ".($i+1));
			
			for($j = 0, $lenJ = rand(20, 40); $j < $lenJ; $j++){
				$content->appendParagraph("P".($i+1).".".($j+1)." TEXT TEXT TEXT");
			}
			
			$content->appendPageBreak();
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
