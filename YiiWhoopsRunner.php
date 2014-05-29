<?php
use Whoops\Exception\Inspector;

class YiiWhoopsRunner extends \Whoops\Run {

	protected function getInspector(Exception $exception) {
		require 'YiiInspector.php';
		return new YiiInspector($exception);
	}

}