<?php

/**
 * ZIP
 *
 * Archive compressed data.
 *
 * @package core
 * @author stefano.azzolini@caffeinalab.com
 * @copyright Caffeina srl - 2015 - http://caffeina.it
 */

class ZIP {
	use Module;

	public $file,
		     $name,
		     $zip;

	public static function create($name=''){
		return new ZIP($name);
	}

	public function __construct($name=''){
		$this->name = preg_replace('/\.zip$/','',($name?:tempnam(sys_get_temp_dir(), 'ZExp').'-archive'));
		$this->file = $this->name . '.zip';
		if (!preg_match('~^/|\./|\.\./~',$this->file)) $this->file = './'.$this->file;
		$this->zip  = new \ZipArchive;
		if ( true !== ($e = $this->zip->open($this->file,
			\ZipArchive::CREATE || \ZipArchive::OVERWRITE
		))) {
			throw new Exception("Error opening temp ZIP file [".($this->file)."] Code $e", 1);
		}
	}

	public function __destruct(){
		$this->close();
	}

	public function path(){
		return $this->file;
	}

	public function write($filename, $data){
		$this->zip->addFromString($filename, $data);
		return $this;
	}

	public function close(){
		if($this->zip) @$this->zip->close();
		return $this;
	}

	public function addDirectory($folder, $root=null) {
		$folder = rtrim($folder,'/');
		if (null === $root) {
			$root   = dirname($folder);
			$folder = basename($folder);
		}
		$this->zip->addEmptyDir($folder);
		$nodes = glob("$root/$folder/*"); 
		foreach ($nodes as $node) { 
			if (is_dir($node)) { 
				$this->addDirectory(str_replace($root,'',$node),$root); 
			} else if (is_file($node))  { 
				$this->zip->addFile($node,str_replace($root,'',$node)); 
			} 
		} 

		return $this;
	}

	public function download(){
		@$this->zip->close();
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment;filename="'.$this->name.'"',true);
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: '.filesize($this->file));
		while(ob_get_level()) ob_end_clean();
		readfile($this->file);
		exit;
	}

}
