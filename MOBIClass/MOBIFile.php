<?php
/**
 * This is the way MOBI files should be created if you want all features (TOC, images).
 */

class MOBIFile extends ContentProvider {
	const PARAGRAPH = 0;
	const H2 = 1;
	const H3 = 2;
	const IMAGE = 3;
	
	private $settings = array("title" => "Unknown Title");
	private $parts = array();
	private $images = array();
	
	public function getTextData(){
		$prefix = "<html><head><guide><reference title='CONTENT' type='toc' filepos=0000000000 /></guide></head><body>";
		
		$title = "<h1>".$this->settings["title"]."</h1>";
		
		list($text, $entries) = $this->generateText();
		
		//Generate TOC to get the right length
		$toc = $this->generateTOC($entries);
		
		//Generate the real TOC
		$toc = $this->generateTOC($entries, strlen($prefix)+strlen($toc)+strlen($title));
		
		$suffix = "</body></html>";
		
		return $prefix.$toc.$title.$text.$suffix;
	}
	
	public function generateText(){
		$str = "";
		$entries = array();
		
		for($i = 0; $i < sizeof($this->parts); $i++){
			list($type, $data) = $this->parts[$i];
			switch($type){
				case self::PARAGRAPH:
					$str .= "<p>".$data."</p>";
					break;
				case self::H2:
					$entries[] = array("level" => 2, "length" => strlen($str), "title" => $data);
					$str .= "<h2>".$data."</h2>";
					break;
				case self::H3:
					$entries[] = array("level" => 3, "length" => strlen($str), "title" => $data);
					$str .= "<h3>".$data."</h3>";
					break;
				case self::IMAGE:
					$str .= "<img recindex=".str_pad($data+1, 10, "0", STR_PAD_LEFT)." />";
					break;
			}
		}
		return array($str, $entries);
	}
	
	public function generateTOC($entries, $base = 0){
		$toc = "<h2>Contents</h2>";
		$toc .= "<blockquote><table summary='Table of Contents'><col/><tbody>";
		for($i = 0, $len = sizeof($entries); $i < $len; $i++){
			$entry = $entries[$i];
			$pos = str_pad($entry["length"]+$base, 10, "0", STR_PAD_LEFT);
			$toc .= "<tr><td><a filepos='".$pos."'>".$entry["title"]."</a></td></tr>";
		}
		return $toc."</tbody></b></table></blockquote>";
	}
	
	public function getImages(){
		return $this->images;
	}
	
	public function getMetaData(){
		return $this->settings;
	}
	
	/**
	 * Change the file's settings. For example set("author", "John Doe") or set("title", "The adventures of John Doe").
	 * @param $key Key of the setting to insert.
	 */
	public function set($key, $value){
		$this->settings[$key] = $value;
	}
	
	/**
	 * Get the file's settings.
	 */
	public function get($key){
		return $this->settings[$key];
	}
	
	/**
	 * Append a paragraph of text to the file.
	 * @param string $text The text to insert.
	 */
	public function appendParagraph($text){
		$this->parts[] = array(self::PARAGRAPH, $text);
	}
	
	/**
	 * Append a chapter title (H2)
	 * @param string $title The title to insert.
	 */
	public function appendChapterTitle($title){
		$this->parts[] = array(self::H2, $title);
	}
	
	/**
	 * Append a section title (H3)
	 * @param string $title The title to insert.
	 */
	public function appendSectionTitle($title){
		$this->parts[] = array(self::H3, $title);
	}
	
	/**
	 * Append an image.
	 * @param resource $img An image file (for example, created by `imagecreate`)
	 */
	public function appendImage($img){
		$imgIndex = sizeof($this->images);
		$this->images[] = new FileRecord(new Record(ImageHandler::CreateImage($img)));
		$this->parts[] = array(self::IMAGE, $imgIndex);
	}
}