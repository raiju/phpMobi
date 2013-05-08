phpMobi file generator
======================

phpMobi is a php script that can generate .mobi files from valid html
files. While this was meant as an experiment, this tool works quite
well and can be used to generate mobipocket files from most news articles.

IMPORTANT: Do NOT use this on a public web server: most of it was coded in
a weekend, with no testing and no special attention to security. Also, as no official
documentation for the MOBI file format is available, there will be some bugs/problems
in the generated files, but it works for relatively simple documents on the Kindle
previewer and the Kindle 3.

MobiPocket is an eBook format created by Mobipocket SA. This tool also
uses a php readability port made by [Keyvan Minoukadeh](http://www.keyvan.net/2010/08/php-readability/).

Code sample
------------

See index.php for an example of using this program.

Sending an online article as a download:

	//Create the MOBI object
	$mobi = new MOBI();
	
	//Set the content provider
	$content = new OnlineArticle("URL");
	$mobi->setContentProvider($content);
	
	//Get title and make it a 12 character long url-safe filename
	$title = $mobi->getTitle();
	if($title === false)
		$title = "file";
	
	$title = urlencode(str_replace(" ", "_", strtolower(substr($title, 0, 12))));
	
	//Send the mobi file as download
	$mobi->download($title.".mobi");

Using a previously generated/downloaded html file (will not download any images!):

	$data = "<html>...</html>";
	$options = array(
		"title" => "Local document",
	  	"author" => "Author name",
	  	"subject" => "Subject"
	);
	
	//Create the MOBI object
	$mobi = new MOBI();
	
	//Set the data
	$mobi->setData($data);
	$mobi->setOptions($options);
	
	//Save the mobi file locally
	$mobi->save($options["title"].".mobi");

Implementation
--------------

This code was implemented while reverse-engineering the MobiPocket format.
Therefore this code absolutely isn't optimized for speed, but rather for
easy changes, as getting it to produce valid files was quite fiddly.

Features
--------

Modular content provider system:
	Adding a new data source can be done by extending the ContentProvider
	class. See the OnlineArticle class for a simple but complete
	implementation of such a system.

Image support:
	By default, the online article downloader (and any other content
	provider that supports images) will download images and integrate them
	into the mobi file.

Partial UTF-8 support:
	In practice UTF-8 just works, but there are some unhandled corner
	cases (see missing features).

Missing Features
----------------

Compression:
	This won't be implemented (or if it is, only to serve as a
	reference of the format).

Different eBook types:
	MobiPocket supports other formats/layouts, such as newspaper-like
	formats. At the moment only the book layout has been implemented.

Full UTF-8 support:
	UTF-8 should work most of the time (it worked every time I
	tested it), but there might be some problems when the character
	is split over two "records".

License
-------
This code is released under the Apache license (version 2.0)
