<?php

namespace Adm;

class InputBag implements \ArrayAccess
{
	private $post;

	public function __construct($request)
	{
		$this->post = $request;
	}

	public function input($key,$defaultValue)
	{
		if (isset($this->post[$key])) {
			return $this->post[$key];
		} else {
			return $defaultValue;
		}
	}

	public function get($key,$defaultValue)
	{
		return $this->input($key,$defaultValue);
	}

	public function offsetGet($offset)
	{
		return $this->input($offset,false);
	}

	public function offsetSet($offset,$value)
	{
        $this->post[$offset] = $value;
	}

	public function offsetExists($offset)
	{
        return isset($this->post[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->post[$offset]);
    }

}