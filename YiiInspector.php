<?php
use Whoops\Exception\ErrorException;
use Whoops\Exception\FrameCollection;

class YiiInspector extends \Whoops\Exception\Inspector {

	public function getFrames() {
		//Removing all object wrappers and getting list of raw frames
		$frames_obj = parent::getFrames()->getArray();
		$frames = array_map(function($frame) { return $frame->getRawFrame(); }, $frames_obj);

		//Skipping Whoops and Yii handlers
		$frames = array_slice($frames, 5);

		return new FrameCollection($frames);
	}

}