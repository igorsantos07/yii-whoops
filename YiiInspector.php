<?php
use Whoops\Exception\FrameCollection;

class YiiInspector extends \Whoops\Exception\Inspector {

	public function getFrames() {
		$frames_obj = parent::getFrames()->getArray();

		//If it's a PHP error we can skip all classes before it, as those are handlers' exceptions
		if ($frames_obj[0]->getClass() == 'Whoops\Exception\ErrorException') {

			//Removing all object wrappers and getting list of raw frames
			$frames = array_map(function($frame) { return $frame->getRawFrame(); }, $frames_obj);

			$removable_keys = [0];
			while(array_key_exists('class', next($frames)))
				$removable_keys[] = key($frames);

			foreach ($removable_keys as $key)
				unset($frames[$key]);
			$frames = array_merge($frames);

			$frames_obj = new FrameCollection($frames);
		}

		return $frames_obj;
	}

}