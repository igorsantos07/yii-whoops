<?php
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;

class WhoopsErrorHandler extends CErrorHandler {

	/**
	 * Whoops instance.
	 * @var Whoops
	 */
	protected $whoops;

	/**
	 * Page title in case of non-AJAX requests. Whoops already have a default "Whoops! There was an error."
	 * @var string
	 */
	public $pageTitle;

	protected $defaultDisabledLogRoutes = array('YiiDebugToolbarRoute');

	protected $disabledLogRoutes = array();

	/**
	 * Weirdly {@link CErrorHandler::error} is private, so we need a new property =.=
	 * @var CErrorEvent|Exception
	 */
	protected $problem;

	protected $handled = false;

	public function handled() {
		$this->handled = true;
	}

	/**
	 * Instantiate Whoops with the correct handlers.
	 */
	public function __construct() {
		require 'YiiWhoopsRunner.php';
		$this->whoops = new YiiWhoopsRunner;

		if (Yii::app()->request->isAjaxRequest) {
			$this->whoops->pushHandler(new JsonResponseHandler);
		}
		else {
			$page_handler = new PrettyPageHandler;
			if ($this->pageTitle)
				$page_handler->setPageTitle($this->pageTitle);

			$reordered_tables = array(
				'Request information'   => static::createRequestTable(),
				"GET Data"              => $_GET,
				"POST Data"             => $_POST,
				"Files"                 => $_FILES,
				"Cookies"               => $_COOKIE,
				"Session"               => isset($_SESSION)? $_SESSION : array(),
				"Environment Variables" => $_ENV,
				"Server/Request Data"   => $_SERVER,
			);
			foreach ($reordered_tables as $label => $data)
				$page_handler->addDataTable($label, $data);

			$this->whoops->pushHandler($page_handler);
		}
	}

	protected static function createRequestTable() {
		$request = array();
		$header  = array();
		if (isset($_SERVER['SERVER_PROTOCOL'])) $header[] = $_SERVER['SERVER_PROTOCOL'];
		if (isset($_SERVER['REQUEST_METHOD'])) $header[] = $_SERVER['REQUEST_METHOD'];
		if (isset($_SERVER['HTTP_HOST'])) $header[] = $_SERVER['HTTP_HOST'];
		$request['Request'] = implode('  ', $header);

		if (isset($_SERVER['REQUEST_URI'])) $request['Resource'] = ltrim($_SERVER['REQUEST_URI'], '/');

		if (isset($_SERVER['SCRIPT_FILENAME'])) $request['Entry script'] = $_SERVER['SCRIPT_FILENAME'];

		$ips = array();
		if (isset($_SERVER['SERVER_ADDR'])) $ips[] = 'Server: '.$_SERVER['SERVER_ADDR'];
		if (isset($_SERVER['REMOTE_ADDR'])) $ips[] = 'Client: '.$_SERVER['REMOTE_ADDR'];
		$request['IPs'] = implode('  ||  ', $ips);

		if (isset($_SERVER['HTTP_USER_AGENT'])) $request['User agent'] = $_SERVER['HTTP_USER_AGENT'];
		if (isset($_SERVER['REQUEST_TIME'])) $request['Request time'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

		return $request;
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
		if ($this->beforeHandling($event)) {

			if($this->isAjaxRequest()) {
				$app->displayError($event->code, $event->message, $event->file, $event->line);
			}
			elseif(!YII_DEBUG) {
				parent::render('error', $event);
			}
			else {
				try {
					$this->whoops->handleError($event->code, $event->message, $event->file, $event->line);
				}
				catch (\Exception $e) {
					$this->handleException($e);
				}
			}
		}
	}

	/**
	 * Forwards an exception to Whoops.
	 * @param Exception $exception
	 */
	protected function handleException($exception) {
		if ($this->beforeHandling($exception)) {
			if(!headers_sent()) {
				$code = ($exception instanceof CHttpException)? $exception->statusCode : 500;
				$msg  = $this->getHttpHeader($code, get_class($exception));
				header("{$_SERVER['SERVER_PROTOCOL']} $code $msg");
			}

			if($exception instanceof CHttpException || !YII_DEBUG)
				parent::render('error', $exception);
			else {
				if($this->isAjaxRequest()) {
					$app->displayException($exception);
				}
				else {
					if ($this->errorAction)
						Yii::app()->runController($this->errorAction);

					if (!$this->handled)
						$this->whoops->handleException($exception);
				}
			}
		}

	}

	protected function beforeHandling($problem) {
		$this->problem = $problem;
		$this->disableLogRoutes();

		if (Yii::app() instanceof CWebApplication) {
			return true;
		}
		else {
			if ($problem instanceof \Exception)
				Yii::app()->displayException($problem);
			elseif ($problem instanceof CErrorEvent)
				$app->displayError($problem->code, $problem->message, $problem->file, $problem->line);

			return false;
		}
	}

	/**
	 * @return CErrorEvent|Exception
	 */
	public function getError() {
		return $this->problem;
	}

}
