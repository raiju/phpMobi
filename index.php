<?php
include("MOBIClass/MOBI.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title></title>
	</head>
	<body>
		<?php
		$options = array(
			"title"=>"GROUP BY vs ORDER BY",
			"author"=>"thetricky.net",
			"subject"=>"MySql"
		);

		$mobi = new MOBI();
		$mobi->setFileSource("test/test.html");
		//$mobi->setInternetSource("URL");
		$mobi->setOptions($options);

		$mobi->save("test/test.mobi");
		?>
	</body>
</html>
