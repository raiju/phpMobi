<?php

/**
 * Helper class to help with creating records from a
 * long data stream
 *
 * @author Sander
 */
class RecordFactory {
	/**
	 * Settings for the record factory
	 * @var Settings
	 */
	private $settings;

	/**
	 * Create the helper class
	 * @param Settings $settings The Settings to be used for the records
	 */
	public function __construct($settings){
		$this->settings = $settings;
	}

	/**
	 * Create records from a data string
	 * @param string $data
	 * @return array(Record)
	 */
	public function createRecords($data){
		$records = array();
		$size = $this->settings->get("recordSize");
		$compression = $this->settings->get("compression");

		$dataEntries = mb_str_split($data, $size);

		for($i = 0, $len = sizeof($dataEntries); $i < $len; $i++){
			$cur = $dataEntries[$i];
			
			$dataEntries[$i] = $cur;
			$records[$i] = new Record($dataEntries[$i]);
			$records[$i]->compress($compression);
		}

		return $records;
	}

	public function createEOFRecord(){
		return new Record(0xe98e0d0a);
	}

	public function createFCISRecord($textLength){
		$r = "FCIS";
		$r .= $this->asString(20, 4);
		$r .= $this->asString(16, 4);
		$r .= $this->asString(1, 4);
		$r .= $this->asString(0, 4);
		$r .= $this->asString($textLength, 4);
		$r .= $this->asString(0, 4);
		$r .= $this->asString(32, 4);
		$r .= $this->asString(8, 4);
		$r .= $this->asString(1, 2);
		$r .= $this->asString(1, 2);
		$r .= $this->asString(0, 4);
		return new Record($r);
	}

	public function createFLISRecord(){
		$r = "FLIS";
		$r .= $this->asString(8, 4);
		$r .= $this->asString(65, 2);
		$r .= $this->asString(0, 2);
		$r .= $this->asString(0, 4);
		$r .= $this->asString(-1, 4);
		$r .= $this->asString(1, 2);
		$r .= $this->asString(3, 2);
		$r .= $this->asString(3, 4);
		$r .= $this->asString(1, 4);
		$r .= $this->asString(-1, 4);
		return new Record($r);
	}
	
	private function asString($int, $size){
		$out = "";
		for($i = 0; $i < $size; $i++){
			if($i > 0) $out = " ".$out;
			$byte = dechex($int & 0xFF);
			if(strlen($byte) == 1) $byte = "0".$byte;
			$out = $byte.$out;
			$int = $int >> 8;
		}
		return $out;
	}

	public function __toString() {
		$out = "Record Factory: {\n";
		$out .= "\tRecord Size: ".$this->settings->get("recordSize")."\n";
		$out .= "\tCompression: ".$this->settings->get("compression")."\n";
		$out .= "}";
		return $out;
	}
}

/**
 * Split string in chunks of at most split_length bytes, while respecting multi-byte
 * character boundaries.
 */
function mb_str_split($string, $split_length = 1){
	mb_internal_encoding('UTF-8');
	mb_regex_encoding('UTF-8');

	$split_length = ($split_length <= 0) ? 1 : $split_length;

	$bytes = strlen($string);

	$array = array();
	
	if ($split_length >= $bytes) {
		$array[] = $string;
		return $array;
	}
	
	$i = 0;
	while ($i < $bytes) {
		$cut_string = mb_strcut($string, $i, $split_length);
		$n_bytes = strlen($cut_string);
		
		if ($n_bytes == 0) {
			throw new Exception('Inifite loop in string split detected.');
		}
		$array[] = $cut_string;
		$i += strlen($cut_string);
	}

	return $array;
}
?>
