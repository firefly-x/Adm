<?php

namespace Adm\Utils;

class Uploader {
	
	private $uploadDir;

	private $rewrite = true;

	public function __construct($uploadDir)
	{
		$this->uploadDir = $uploadDir;
	}

	public function save($file)
	{		
		$filename = $file['name'];
		$fullPath = $this->getFullPath($filename);
		if (file_exists($fullPath) && $this->rewrite == false) {
			throw new Exception("Aynı isimde bir dosya bulundu", 1);
		}
		try {
    			$result = move_uploaded_file($file['tmp_name'],$fullPath);
    		} catch (Exception $e) {
    			$result = false;
    		}
    		return array('success' => $result,'fullpath' => $fullPath,'filename' => $filename,'file' => $file);
	}

	public function setRewrite($to)
	{
		$this->rewrite = $to;
	}

	private function getFullPath($filename)
	{
		return $this->uploadDir . DIRECTORY_SEPARATOR . $filename;
	}	

}