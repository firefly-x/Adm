<?php

namespace Adm;

class AnnotationReader
{
	public function read($obj)
	{
		$ref = new \ReflectionClass($obj);
		$props = $ref->getProperties();

		$commands = [];
		foreach ($props as $prop) {
			$comment = trim(substr($prop->getDocComment(), 3, -2));

			if ($comment) {
				foreach (array_filter(explode('@', $comment)) as $commandBlock) {
					$commandBlock = trim($commandBlock);

					$pStart = strpos($commandBlock,'(');
					if ($pStart) {
						$pEnd = strpos($commandBlock,')');
						$command = substr($commandBlock,0,$pStart);

						$params = substr($commandBlock, $pStart+1,-1);
						foreach (explode(',', $params) as $param) {
							$valueSeparator = strpos($param,'=');
							$key = substr($param,0,$valueSeparator);
							$value = substr($param, $valueSeparator+1);
							$cmdObject = (object) array($key => trim($value,'"'));
							$commands[$prop->name] = (object) array($command => $cmdObject);
						}
					} else {
						$commands[$prop->name] = (object) array($commandBlock => true);
					}
				}
			}
		}
		return (object)$commands;
	}

	private function parseParams()
	{

	}
}