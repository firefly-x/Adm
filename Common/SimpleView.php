<?php

namespace Adm\Common;

class SimpleView
{	
	
	protected $viewPath;

	public function __construct($viewPath)
	{
		$this->viewPath = $viewPath;
	}

	public function loadView($viewName,$params = [])
	{
		return $this::load($this->viewPath . $viewName,$params);
	}

	static public function load($filename,$params=[])
	{
		foreach ($params as $key => $value) {
			$$key = $value;
		}
		ob_start();
		include($filename);
		return ob_get_clean();
	}

}