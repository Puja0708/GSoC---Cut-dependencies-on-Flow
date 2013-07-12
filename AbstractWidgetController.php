<?php
namespace TYPO3\Fluid\Core\Widget;

/*
 * This script belongs to the TYPO3 Flow package "Fluid".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * This is the base class for all widget controllers.
 * Basically, it is an ActionController, and it additionally
 * has $this->widgetConfiguration set to the Configuration of the current Widget.
 *
 * @api
 */
abstract class AbstractWidgetController extends ActionController {

  /**
	 * @var array
	 */
	protected $supportedRequestTypes = array('TYPO3\\Fluid\\Core\\Widget\\WidgetRequest');

	/**
	 * Configuration for this widget.
	 *
	 * @var array
	 * @api
	 */
	protected $widgetConfiguration;

	/**
	 * Handles a request. The result output is returned by altering the given response.
	 *
	 * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request The request object
	 * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response The response, modified by this handler
	 * @return void
	 * @api
	 */
	public function processRequest(RequestInterface $request, ResponseInterface $response) {
		$this->widgetConfiguration = $request->getWidgetContext()->getWidgetConfiguration();
		parent::processRequest($request, $response);
	}

	/**
	 * Allows the widget template root path to be overriden via the framework configuration,
	 * e.g. plugin.tx_extension.view.widget.<WidgetViewHelperClassName>.templateRootPath
	 *
	 * @param \TYPO3\Fluid\View\ViewInterface $view
	 * @return void
	 */
	protected function setViewConfiguration(\TYPO3\Fluid\View\ViewInterface $view) {
		$extbaseFrameworkConfiguration = $this->configurationManager->getConfiguration(\TYPO3\Fluid\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
		$widgetViewHelperClassName = $this->request->getWidgetContext()->getWidgetViewHelperClassName();
	}


/**
 * Contract for a dispatchable request.
 *
 * @api
 */
interface RequestInterface {

	/**
	 * Sets the dispatched flag
	 *
	 * @param boolean $flag If this request has been dispatched
	 * @return void
	 * @api
	 */
	public function setDispatched($flag);

	/**
	 * If this request has been dispatched and addressed by the responsible
	 * controller and the response is ready to be sent.
	 *
	 * The dispatcher will try to dispatch the request again if it has not been
	 * addressed yet.
	 *
	 * @return boolean TRUE if this request has been dispatched successfully
	 * @api
	 */
	public function isDispatched();

	/**
	 * Returns the object name of the controller which is supposed to process the
	 * request.
	 *
	 * @return string The controller's object name
	 * @api
	 */
	public function getControllerObjectName();

	/**
	 * Returns the top level Request: the one just below the HTTP request
	 *
	 * @return \TYPO3\Flow\Mvc\RequestInterface
	 * @api
	 */
	public function getMainRequest();

	/**
	 * Checks if this request is the uppermost ActionRequest, just one below the
	 * HTTP request.
	 *
	 * @return boolean
	 * @api
	 */
	public function isMainRequest();

}


/**
 * A generic and very basic response implementation
 *
 * @api
 */
interface ResponseInterface {

	/**
	 * Overrides and sets the content of the response
	 *
	 * @param string $content The response content
	 * @return void
	 * @api
	 */
	public function setContent($content);

	/**
	 * Appends content to the already existing content.
	 *
	 * @param string $content More response content
	 * @return void
	 * @api
	 */
	public function appendContent($content);

	/**
	 * Returns the response content without sending it.
	 *
	 * @return string The response content
	 * @api
	 */
	public function getContent();

	/**
	 * Sends the response
	 *
	 * @return void
	 * @api
	 */
	public function send();
}

}

/**
 * An object representation of a generic message. Usually, you will use Error, Warning or Notice instead of this one.
 *
 * @api
 */
class Message {

	const SEVERITY_NOTICE = 'Notice';
	const SEVERITY_WARNING = 'Warning';
	const SEVERITY_ERROR = 'Error';
	const SEVERITY_OK = 'OK';

	/**
	 * The error message, could also be a key for translation.
	 * @var string
	 */
	protected $message = '';

	/**
	 * An optional title for the message (used eg. in flashMessages).
	 * @var string
	 */
	protected $title = '';

	/**
	 * The error code.
	 * @var integer
	 */
	protected $code = NULL;

	/**
	 * The message arguments. Will be replaced in the message body.
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * The severity of this message ('OK'), overwrite in your own implementation.
	 * @var string
	 */
	protected $severity = self::SEVERITY_OK;

	/**
	 * Constructs this error
	 *
	 * @param string $message An english error message which is used if no other error message can be resolved
	 * @param integer $code A unique error code
	 * @param array $arguments Array of arguments to be replaced in message
	 * @param string $title optional title for the message
	 * @api
	 */
	public function __construct($message, $code = NULL, array $arguments = array(), $title = '') {
		$this->message = $message;
		$this->code = $code;
		$this->arguments = $arguments;
		$this->title = $title;
	}

	/**
	 * Returns the error message
	 *
	 * @return string The error message
	 * @api
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Returns the error code
	 *
	 * @return integer The error code
	 * @api
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * @return array
	 * @api
	 */
	public function getArguments() {
		return $this->arguments;
	}

	/**
	 * @return string
	 * @api
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * @return string
	 * @api
	 */
	public function getSeverity() {
		return $this->severity;
	}

	/**
	 * @return string
	 */
	public function render() {
		if ($this->arguments !== array()) {
			return vsprintf($this->message, $this->arguments);
		} else {
			return $this->message;
		}
	}

	/**
	 * Converts this error into a string
	 *
	 * @return string
	 * @api
	 */
	public function __toString() {
		return $this->render();
	}
}

/**
 * An object representation of a generic error. Subclass this to create
 * more specific errors if necessary.
 *
 * @api
 */
class Error extends Message {

	/**
	 * The severity of this message ('Error').
	 * @var string
	 */
	protected $severity = self::SEVERITY_ERROR;

}

class ActionController extends AbstractController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Reflection\ReflectionService
	 */
	protected $reflectionService;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Mvc\Controller\MvcPropertyMappingConfigurationService
	 */
	protected $mvcPropertyMappingConfigurationService;

	/**
	 * The current view, as resolved by resolveView()
	 *
	 * @var \TYPO3\Flow\Mvc\View\ViewInterface
	 * @api
	 */
	protected $view = NULL;

	/**
	 * Pattern after which the view object name is built if no format-specific
	 * view could be resolved.
	 *
	 * @var string
	 * @api
	 */
	protected $viewObjectNamePattern = '@package\View\@controller\@action@format';

	/**
	 * A list of formats and object names of the views which should render them.
	 *
	 * Example:
	 *
	 * array('html' => 'MyCompany\MyApp\MyHtmlView', 'json' => 'MyCompany\...
	 *
	 * @var array
	 */
	protected $viewFormatToObjectNameMap = array();

	/**
	 * The default view object to use if none of the resolved views can render
	 * a response for the current request.
	 *
	 * @var string
	 * @api
	 */
	protected $defaultViewObjectName = 'TYPO3\Fluid\View\TemplateView';

	/**
	 * Name of the action method
	 *
	 * @var string
	 */
	protected $actionMethodName;

	/**
	 * Name of the special error action method which is called in case of errors
	 *
	 * @var string
	 * @api
	 */
	protected $errorMethodName = 'errorAction';

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Handles a request. The result output is returned by altering the given response.
	 *
	 * @param RequestInterface $request The request object
	 * @param ResponseInterface $response The response, modified by this handler
	 * @return void
	 * @throws \TYPO3\Fluid\Exception\UnsupportedRequestTypeException
	 * @api
	 */
	public function processRequest(RequestInterface $request, ResponseInterface $response) {
		$this->initializeController($request, $response);

		$this->actionMethodName = $this->resolveActionMethodName();

		$this->initializeActionMethodArguments();
		$this->initializeActionMethodValidators();
		$this->mvcPropertyMappingConfigurationService->initializePropertyMappingConfigurationFromRequest($this->request, $this->arguments);

		$this->initializeAction();
		$actionInitializationMethodName = 'initialize' . ucfirst($this->actionMethodName);
		if (method_exists($this, $actionInitializationMethodName)) {
			call_user_func(array($this, $actionInitializationMethodName));
		}

		$this->mapRequestArgumentsToControllerArguments();

		$this->view = $this->resolveView();
		if ($this->view !== NULL) {
			$this->view->assign('settings', $this->settings);
			$this->initializeView($this->view);
		}
		$this->callActionMethod();
	}

	/**
	 * Resolves and checks the current action method name
	 *
	 * @return string Method name of the current action
	 * @throws \TYPO3\Flow\Mvc\Exception\NoSuchActionException
	 */
	protected function resolveActionMethodName() {
		$actionMethodName = $this->request->getControllerActionName() . 'Action';
		if (!is_callable(array($this, $actionMethodName))) {
			throw new \TYPO3\Fluid\Exception\NoSuchActionException('An action "' . $actionMethodName . '" does not exist in controller "' . get_class($this) . '".', 1186669086);
		}
		return $actionMethodName;
	}

	/**
	 * Implementation of the arguments initialization in the action controller:
	 * Automatically registers arguments of the current action
	 *
	 * Don't override this method - use initializeAction() instead.
	 *
	 * @return void
	 * @throws \TYPO3\Flow\Mvc\Exception\InvalidArgumentTypeException
	 * @see initializeArguments()
	 */
	protected function initializeActionMethodArguments() {
		$actionMethodParameters = static::getActionMethodParameters($this->objectManager);
		if (isset($actionMethodParameters[$this->actionMethodName])) {
			$methodParameters = $actionMethodParameters[$this->actionMethodName];
		} else {
			$methodParameters = array();
		}

		$this->arguments->removeAll();
		foreach ($methodParameters as $parameterName => $parameterInfo) {
			$dataType = NULL;
			if (isset($parameterInfo['type'])) {
				$dataType = $parameterInfo['type'];
			} elseif ($parameterInfo['array']) {
				$dataType = 'array';
			}
			if ($dataType === NULL) {
				throw new \TYPO3\Fluid\Exception\InvalidArgumentTypeException('The argument type for parameter $' . $parameterName . ' of method ' . get_class($this) . '->' . $this->actionMethodName . '() could not be detected.', 1253175643);
			}
			$defaultValue = (isset($parameterInfo['defaultValue']) ? $parameterInfo['defaultValue'] : NULL);
			$this->arguments->addNewArgument($parameterName, $dataType, ($parameterInfo['optional'] === FALSE), $defaultValue);
		}
	}

	/**
	 * Returns a map of action method names and their parameters.
	 *
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 * @return array Array of method parameters by action name
	 * @Flow\CompileStatic
	 */
	static public function getActionMethodParameters($objectManager) {
		$reflectionService = $objectManager->get('TYPO3\Flow\Reflection\ReflectionService');

		$result = array();

		$className = get_called_class();
		$methodNames = get_class_methods($className);
		foreach ($methodNames as $methodName) {
			if (strlen($methodName) > 6 && strpos($methodName, 'Action', strlen($methodName) - 6) !== FALSE) {
				$result[$methodName] = $reflectionService->getMethodParameters($className, $methodName);
			}
		}

		return $result;
	}

	/**
	 * Adds the needed validators to the Arguments:
	 *
	 * - Validators checking the data type from the @param annotation
	 * - Custom validators specified with validate annotations.
	 * - Model-based validators (validate annotations in the model)
	 * - Custom model validator classes
	 *
	 * @return void
	 */
	protected function initializeActionMethodValidators() {
		$validateGroupAnnotations = static::getActionValidationGroups($this->objectManager);
		if (isset($validateGroupAnnotations[$this->actionMethodName])) {
			$validationGroups = $validateGroupAnnotations[$this->actionMethodName];
		} else {
			$validationGroups = array('Default', 'Controller');
		}

		$actionMethodParameters = static::getActionMethodParameters($this->objectManager);
		if (isset($actionMethodParameters[$this->actionMethodName])) {
			$methodParameters = $actionMethodParameters[$this->actionMethodName];
		} else {
			$methodParameters = array();
		}
		$actionValidateAnnotations = static::getActionValidateAnnotationData($this->objectManager);
		if (isset($actionValidateAnnotations[$this->actionMethodName])) {
			$validateAnnotations = $actionValidateAnnotations[$this->actionMethodName];
		} else {
			$validateAnnotations = array();
		}
		$parameterValidators = $this->validatorResolver->buildMethodArgumentsValidatorConjunctions(get_class($this), $this->actionMethodName, $methodParameters, $validateAnnotations);

		foreach ($this->arguments as $argument) {
			$validator = $parameterValidators[$argument->getName()];

			$baseValidatorConjunction = $this->validatorResolver->getBaseValidatorConjunction($argument->getDataType(), $validationGroups);
			if (count($baseValidatorConjunction) > 0) {
				$validator->addValidator($baseValidatorConjunction);
			}
			$argument->setValidator($validator);
		}
	}

	/**
	 * Returns a map of action method names and their validation groups.
	 *
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 * @return array Array of validation groups by action method name
	 * @Flow\CompileStatic
	 */
	static public function getActionValidationGroups($objectManager) {
		$reflectionService = $objectManager->get('TYPO3\Flow\Reflection\ReflectionService');

		$result = array();

		$className = get_called_class();
		$methodNames = get_class_methods($className);
		foreach ($methodNames as $methodName) {
			if (strlen($methodName) > 6 && strpos($methodName, 'Action', strlen($methodName) - 6) !== FALSE) {
				$validationGroupsAnnotation = $reflectionService->getMethodAnnotation($className, $methodName, 'TYPO3\Flow\Annotations\ValidationGroups');
				if ($validationGroupsAnnotation !== NULL) {
					$result[$methodName] = $validationGroupsAnnotation->validationGroups;
				}
			}
		}

		return $result;
	}

	/**
	 * Returns a map of action method names and their validation parameters.
	 *
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 * @return array Array of validate annotation parameters by action method name
	 * @Flow\CompileStatic
	 */
	static public function getActionValidateAnnotationData($objectManager) {
		$reflectionService = $objectManager->get('TYPO3\Flow\Reflection\ReflectionService');

		$result = array();

		$className = get_called_class();
		$methodNames = get_class_methods($className);
		foreach ($methodNames as $methodName) {
			if (strlen($methodName) > 6 && strpos($methodName, 'Action', strlen($methodName) - 6) !== FALSE) {
				$validateAnnotations = $reflectionService->getMethodAnnotations($className, $methodName, 'TYPO3\Flow\Annotations\Validate');
				$result[$methodName] = array_map(function($validateAnnotation) {
					return array(
						'type' => $validateAnnotation->type,
						'options' => $validateAnnotation->options,
						'argumentName' => $validateAnnotation->argumentName,
					);
				}, $validateAnnotations);
			}
		}

		return $result;
	}

	/**
	 * Initializes the controller before invoking an action method.
	 *
	 * Override this method to solve tasks which all actions have in
	 * common.
	 *
	 * @return void
	 * @api
	 */
	protected function initializeAction() {
	}

	/**
	 * Calls the specified action method and passes the arguments.
	 *
	 * If the action returns a string, it is appended to the content in the
	 * response object. If the action doesn't return anything and a valid
	 * view exists, the view is rendered automatically.
	 *
	 * @return void
	 */
	protected function callActionMethod() {
		$preparedArguments = array();
		foreach ($this->arguments as $argument) {
			$preparedArguments[] = $argument->getValue();
		}

		$validationResult = $this->arguments->getValidationResults();

		if (!$validationResult->hasErrors()) {
			$actionResult = call_user_func_array(array($this, $this->actionMethodName), $preparedArguments);
		} else {
			$actionIgnoredArguments = static::getActionIgnoredValidationArguments($this->objectManager);
			if (isset($actionIgnoredArguments[$this->actionMethodName])) {
				$ignoredArguments = $actionIgnoredArguments[$this->actionMethodName];
			} else {
				$ignoredArguments = array();
			}

				// if there exists more errors than in ignoreValidationAnnotations_=> call error method
				// else => call action method
			$shouldCallActionMethod = TRUE;
			foreach ($validationResult->getSubResults() as $argumentName => $subValidationResult) {
				if (!$subValidationResult->hasErrors()) {
					continue;
				}

				if (array_search($argumentName, $ignoredArguments) !== FALSE) {
					continue;
				}

				$shouldCallActionMethod = FALSE;
			}

			if ($shouldCallActionMethod) {
				$actionResult = call_user_func_array(array($this, $this->actionMethodName), $preparedArguments);
			} else {
				$actionResult = call_user_func(array($this, $this->errorMethodName));
			}
		}

		if ($actionResult === NULL && $this->view instanceof \TYPO3\Flow\Mvc\View\ViewInterface) {
			$this->response->appendContent($this->view->render());
		} elseif (is_string($actionResult) && strlen($actionResult) > 0) {
			$this->response->appendContent($actionResult);
		} elseif (is_object($actionResult) && method_exists($actionResult, '__toString')) {
			$this->response->appendContent((string)$actionResult);
		}
	}

	/**
	 * @param \TYPO3\Flow\Object\ObjectManagerInterface $objectManager
	 * @return array Array of arguments ignored for validation by action method name
	 * @Flow\CompileStatic
	 */
	static public function getActionIgnoredValidationArguments($objectManager) {
		$reflectionService = $objectManager->get('TYPO3\Flow\Reflection\ReflectionService');

		$result = array();

		$className = get_called_class();
		$methodNames = get_class_methods($className);
		foreach ($methodNames as $methodName) {
			if (strlen($methodName) > 6 && strpos($methodName, 'Action', strlen($methodName) - 6) !== FALSE) {
				$ignoreValidationAnnotations = $reflectionService->getMethodAnnotations($className, $methodName, 'TYPO3\Flow\Annotations\IgnoreValidation');
				$ignoredArguments = array_map(function($annotation) { return $annotation->argumentName; }, $ignoreValidationAnnotations);
				if ($ignoredArguments !== array()) {
					$result[$methodName] = $ignoredArguments;
				}
			}
		}

		return $result;
	}

	/**
	 * Prepares a view for the current action and stores it in $this->view.
	 * By default, this method tries to locate a view with a name matching
	 * the current action.
	 *
	 * @return \TYPO3\Fluid\View\ViewInterface the resolved view
	 * @api
	 * @throws \TYPO3\Fluid\Exception\ViewNotFoundException if no view can be resolved
	 */
	protected function resolveView() {
		$viewObjectName = $this->resolveViewObjectName();
		if ($viewObjectName !== FALSE) {
			$view = $this->objectManager->get($viewObjectName);
		} elseif ($this->defaultViewObjectName != '') {
			$view = $this->objectManager->get($this->defaultViewObjectName);
		}
		if (!isset($view)) {
			throw new \TYPO3\Fluid\Exception\ViewNotFoundException(sprintf('Could not resolve view for action "%s" in controller "%s"', $this->request->getControllerActionName(), get_class($this)), 1355153185);
		}
		if (!$view instanceof \TYPO3\Fluid\View\ViewInterface) {
			throw new \TYPO3\Fluid\Exception\ViewNotFoundException(sprintf('View has to be of type ViewInterface, got "%s" in action "%s" of controller "%s"', get_class($view), $this->request->getControllerActionName(), get_class($this)), 1355153188);
		}
		$view->setControllerContext($this->controllerContext);
		return $view;
	}

	/**
	 * Determines the fully qualified view object name.
	 *
	 * @return mixed The fully qualified view object name or FALSE if no matching view could be found.
	 * @api
	 */
	protected function resolveViewObjectName() {
		$possibleViewObjectName = $this->viewObjectNamePattern;
		$packageKey = $this->request->getControllerPackageKey();
		$subpackageKey = $this->request->getControllerSubpackageKey();
		$format = $this->request->getFormat();

		if ($subpackageKey !== NULL && $subpackageKey !== '') {
			$packageKey .= '\\' . $subpackageKey;
		}
		$possibleViewObjectName = str_replace('@package', str_replace('.', '\\', $packageKey), $possibleViewObjectName);
		$possibleViewObjectName = str_replace('@controller', $this->request->getControllerName(), $possibleViewObjectName);
		$possibleViewObjectName = str_replace('@action', $this->request->getControllerActionName(), $possibleViewObjectName);

		$viewObjectName = $this->objectManager->getCaseSensitiveObjectName(strtolower(str_replace('@format', $format, $possibleViewObjectName)));
		if ($viewObjectName === FALSE) {
			$viewObjectName = $this->objectManager->getCaseSensitiveObjectName(strtolower(str_replace('@format', '', $possibleViewObjectName)));
		}
		if ($viewObjectName === FALSE && isset($this->viewFormatToObjectNameMap[$format])) {
			$viewObjectName = $this->viewFormatToObjectNameMap[$format];
		}
		return $viewObjectName;
	}

	/**
	 * A special action which is called if the originally intended action could
	 * not be called, for example if the arguments were not valid.
	 *
	 * The default implementation sets a flash message, request errors and forwards back
	 * to the originating action. This is suitable for most actions dealing with form input.
	 *
	 * @return string
	 * @api
	 */
	protected function errorAction() {
		$errorFlashMessage = $this->getErrorFlashMessage();
		if ($errorFlashMessage !== FALSE) {
			$this->flashMessageContainer->addMessage($errorFlashMessage);
		}
		$referringRequest = $this->request->getReferringRequest();
		if ($referringRequest !== NULL) {
			$subPackageKey = $referringRequest->getControllerSubpackageKey();
			if ($subPackageKey !== NULL) {
				rtrim($packageAndSubpackageKey = $referringRequest->getControllerPackageKey() . '\\' . $referringRequest->getControllerSubpackageKey(), '\\');
			} else {
				$packageAndSubpackageKey = $referringRequest->getControllerPackageKey();
			}
			$argumentsForNextController = $referringRequest->getArguments();
			$argumentsForNextController['__submittedArguments'] = $this->request->getArguments();
			$argumentsForNextController['__submittedArgumentValidationResults'] = $this->arguments->getValidationResults();

			$this->forward($referringRequest->getControllerActionName(), $referringRequest->getControllerName(), $packageAndSubpackageKey, $argumentsForNextController);
		}

		$message = 'An error occurred while trying to call ' . get_class($this) . '->' . $this->actionMethodName . '().' . PHP_EOL;
		foreach ($this->arguments->getValidationResults()->getFlattenedErrors() as $propertyPath => $errors) {
			foreach ($errors as $error) {
				$message .= 'Error for ' . $propertyPath . ':  ' . $error->render() . PHP_EOL;
			}
		}

		return $message;
	}

	/**
	 * A template method for displaying custom error flash messages, or to
	 * display no flash message at all on errors. Override this to customize
	 * the flash message in your action controller.
	 *
	 * @return Message The flash message or FALSE if no flash message should be set
	 * @api
	 */
	protected function getErrorFlashMessage() {
		return new Error('An error occurred while trying to call %1$s->%2$s()', NULL, array(get_class($this), $this->actionMethodName));
	}
}

?>
