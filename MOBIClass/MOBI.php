<?php
require_once(dirname(__FILE__)."/readability/Readability.php");
require_once(dirname(__FILE__).'/EXTHHelper.php');
require_once(dirname(__FILE__).'/FileObject.php');
require_once(dirname(__FILE__).'/FileByte.php');
require_once(dirname(__FILE__).'/FileDate.php');
require_once(dirname(__FILE__).'/FileElement.php');
require_once(dirname(__FILE__).'/FileInt.php');
require_once(dirname(__FILE__).'/FileRecord.php');
require_once(dirname(__FILE__).'/FileShort.php');
require_once(dirname(__FILE__).'/FileString.php');
require_once(dirname(__FILE__).'/FileTri.php');
require_once(dirname(__FILE__).'/PalmRecord.php');
require_once(dirname(__FILE__).'/Prc.php');
require_once(dirname(__FILE__).'/Record.php');
require_once(dirname(__FILE__).'/RecordFactory.php');
require_once(dirname(__FILE__).'/Settings.php');
require_once(dirname(__FILE__).'/constants.php');

/**
 * Description of MOBI.
 *
 * Usage:
 * include("MOBIClass/MOBI.php");
 *
 * $mobi = new MOBI();
 *
 * //Then use one of the following ways to prepare information (it should be in the form of valid html)
 * $mobi->setInternetSource($url);		//Load URL, the result will be cleaned using a Readability port
 * $mobi->setFileSource($file);			//Load a local file without any extra changes
 * $mobi->setData($data);				//Load data
 *
 * //If you want, you can set some optional settings
 * $options = array(
 *		"title"=>"Insert title here",
 *		"author"=>"Author"
 * );
 * $mobi->setOptions($options);
 *
 * //Then there are two ways to output it:
 * $mobi->save($file);					//Save the file locally
 * $mobi->download($name);				//Let the client download the file, make sure the page
 *										//that calls it doesn't output anything, otherwise it might
 *										//conflict with the download. $name contains the file name,
 *										//usually something like "title.mobi" (where the title should
 *										//be cleaned so as not to contain illegal characters).
 *
 *
 * @author Sander Kromwijk
 */
class MOBI {
	private $source = false;
	private $optional = array();
	
	public function __construct(){

	}

	/**
	 * Set an internet source. The result will be automatically cleaned using Keyvan Minoukadeh's
	 * port of the readability software.
	 * @param string $url URL of the article
	 */
	public function setInternetSource($url){
		if (!preg_match('!^https?://!i', $url)) $url = 'http://'.$url;
		$html = utf8_encode(file_get_contents($url));
		$r = new Readability($html, $url);
		$r->init();
		$this->setData("<html><head></head><body>".$r->articleContent->innerHTML."</body></html>");
	}

	/**
	 * Set a local file as source
	 * @param string $file Path to the file
	 */
	public function setFileSource($file){
		$this->setData(file_get_contents($file));
	}

	/**
	 * Set the data to use
	 * @param string $data Data to put in the file
	 */
	public function setData($data){
		$this->source = iconv('UTF-8', 'US-ASCII//TRANSLIT', $data);
	}

	/**
	 * Set options, usually for things like titles, authors, etc...
	 * @param array $options Options to set
	 */
	public function setOptions($options){
		$this->optional = $options;
	}

	/**
	 * Prepare the prc file
	 * @return Prc The file that can be used to be saved/downloaded
	 */
	private function preparePRC(){
		if($data === false){
			throw new Exception("No data set");
		}
		$data = $this->source;
		$len = strlen($data);

		$settings = new Settings($this->optional);
		$rec = new RecordFactory($settings);
		$dataRecords = $rec->createRecords($data);
		$nRecords = sizeof($dataRecords);
		$mobiHeader = new PalmRecord($settings, $dataRecords, $nRecords, $len);
		array_unshift($dataRecords, $mobiHeader);
		$dataRecords[] = $rec->createFLISRecord();
		$dataRecords[] = $rec->createFCISRecord($len);
		$dataRecords[] = $rec->createEOFRecord();
		return new Prc($settings, $dataRecords);
	}

	/**
	 * Save the file locally
	 * @param string $filename Path to save the file
	 */
	public function save($filename){
		$prc = $this->preparePRC();
		$prc->save($filename);
	}

	/**
	 * Let the client download the file. Warning! No data should be
	 * outputted before or after.
	 * @param string $name Name used for download, usually "title.mobi"
	 */
	public function download($name){
		$prc = $this->preparePRC();
		$data = $prc->serialize();
		$length = strlen($data);

		header("Content-Type: application/force-download");
		header("Content-Disposition: attachment; filename=\"".$name."\"");
		header("Content-Transfer-Encoding: binary");
		header("Accept-Ranges: bytes");
		header("Cache-control: private");
		header('Pragma: private');
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Content-Length: ".$length);

		echo $data;
		//Finished!
	}
}
?>