<?php
use Whoops\Run as Whoops;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;

class WhoopsErrorHandler extends CErrorHandler {

	/**
	 * Whoops instance.
	 *
	 * @var Whoops
	 */
	protected $whoops;

	/**
	 * Page title in case of non-AJAX requests.
	 *
	 * @var string
	 */
	public $pageTitle = 'Whoops! we got a problem here';

	/**
	 * Instantiate Whoops with the correct handlers.
	 */
	public function __construct() {
		$this->whoops = new Whoops;

		if (Yii::app()->request->isAjaxRequest) {
			$this->whoops->pushHandler(new JsonResponseHandler);
		} else {
			$page_handler = new PrettyPageHandler;
			$page_handler->setPageTitle($this->pageTitle);
			$this->whoops->pushHandler($page_handler);
		}
	}

	/**
	 * Forwards an error to Whoops.
	 *
	 * @param CErrorEvent $event
	 */
	protected function handleError($event) {
		$this->whoops->handleError($event->code, $event->message, $event->file, $event->line);
	}

	/**
	 * Forwards an exception to Whoops.
	 *
	 * @param Exception $exception
	 */
	protected function handleException($exception) {
		$this->whoops->handleException($exception);
	}

}