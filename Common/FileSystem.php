<?php

namespace Adm\Common;

class FileSystem
{
	
	public static function generateNewFilename($filename)
	{
		$filename = preg_replace('[^\A-Za-z0-9^\.]', '_', $filename);
		$now = new \DateTime("now");
		$prefix = $now->format('Y_m_d_H_i_s');
		return $prefix . $filename;
	}

}