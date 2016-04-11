<?php

class LinkedStringBuilder {
	private $length = 0;
	private $partSize = array();
	private $parts = array();
	private $links = array();
	private $resolutions = array();
	
	public function addLink($name) {
		$this->links[$name] = $this->length();
	}
	
	public function resolveLink($name, $value) {
		$this->resolutions[$name] = $value;
	}
	
	public function append($string) {
		$len = strlen($string);
		
		$this->length += $len;
		$this->partSize[] = $len;
		$this->parts[] = $string;
	}

	public function replace($from, $to, $replacement) {
		$partStart = 0;
		$partEnd = 0;
		for ($i = 0, $len = sizeof($this->partSize); $i < $len; $i++) {
			$partEnd += $this->partSize[$i];
			if ($partEnd > $from) {
				if ($partEnd < $to) {
					$this->replace($partEnd, $to, substr($replacement, $partEnd - $from));
					$replacement = substr($replacement, 0, $partEnd - $from);
					$to = $partEnd;
				}
				
				$cur = $this->parts[$i];
				
				for ($j = 0; $j < $to - $from; $j++) {
					$cur[$from - $partStart + $j] = $replacement[$j];
				}
				
				$this->parts[$i] = $cur;
				return true;
			}
			$partStart = $partEnd;
		}
		
		throw new Exception("Couldn't replace string (target longer than source?)");
	}
	
	public function length() {
		return $this->length;
	}
	
	public function processLinks() {
		foreach ($this->resolutions as $name => $value) {
			if (isset($this->links[$name])) {
				$start = $this->links[$name];
				$this->replace($start, $start + strlen($value), $value);
				
				unset($this->resolutions[$name]);
			}
		}
	}
	
	public function build() {
		$this->processLinks();
		
		return implode("", $this->parts);
	}
}