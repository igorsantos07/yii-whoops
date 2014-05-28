<?php
use Whoops\Run as Whoops;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;

class WhoopsErrorHandler extends CErrorHandler {

	/**
	 * Whoops instance.
	 * @var Whoops
	 */
	protected $whoops;

	/**
	 * Page title in case of non-AJAX requests.
	 * @var string
	 */
	public $pageTitle = 'Whoops! we got a problem here';

	protected $defaultDisabledLogRoutes = array('YiiDebugToolbarRoute');

	protected $disabledLogRoutes = array();

	/**
	 * Instantiate Whoops with the correct handlers.
	 */
	public function __construct() {
		$this->whoops = new Whoops;

		if (Yii::app()->request->isAjaxRequest) {
			$this->whoops->pushHandler(new JsonResponseHandler);
		}
		else {
			$page_handler = new PrettyPageHandler;
			$page_handler->setPageTitle($this->pageTitle);
			$this->whoops->pushHandler($page_handler);
		}
	}

	/**
	 * Disables some log routes that would output stuff whenever the script finishes, trashing Whoops screen.
	 * @return true
	 */
	protected function disableLogRoutes() {
		//This part verifies if the log routes to disable really exists. If none, simply returns
		$disabled_routes = array_merge($this->defaultDisabledLogRoutes, $this->disabledLogRoutes);
		$continue        = false;
		foreach ($disabled_routes as $route) {
			if (class_exists($route, false)) {
				$continue = true;
				break;
			}
		}
		if (!$continue) return true;

		//Here we actually disable the given routes...
		$total = sizeof(Yii::app()->log->routes);
		for ($i = 0; $i < $total; $i++) {
			foreach ($disabled_routes as $route) {
				if (Yii::app()->log->routes[$i] instanceof $route) {
					Yii::app()->log->routes[$i]->enabled = false;
				}
			}
		}

		return true;
	}

	/**
	 * Forwards an error to Whoops.
	 * @param CErrorEvent $event
	 */
	protected function handleError($event) {
		if (!YII_DEBUG) {
			parent::handleError($event);
			return;
		}
		$this->disableLogRoutes();
		$this->whoops->handleError($event->code, $event->message, $event->file, $event->line);
	}

	/**
	 * Forwards an exception to Whoops.
	 * @param Exception $exception
	 */
	protected function handleException($exception) {
		if ($exception instanceof CHttpException && $this->errorAction!==null || !YII_DEBUG) {
			parent::handleException($exception);
			return;
		}
		$this->disableLogRoutes();
		$this->whoops->handleException($exception);
	}

}
