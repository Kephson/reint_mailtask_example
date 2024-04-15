<?php

namespace RENOLIT\ReintMailtaskExample\ViewHelpers\Link;

/* *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * A view helper for creating links to extbase actions.
 *
 * = Examples =
 *
 * <code title="link to the show-action of the current controller">
 * <f:link.action action="show">action link</f:link.action>
 * </code>
 * <output>
 * <a href="index.php?id=123&tx_myextension_plugin[action]=show&tx_myextension_plugin[controller]=Standard&cHash=xyz">action link</f:link.action>
 * (depending on the current page and your TS configuration)
 * </output>
 */
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Frontend\Utility\EidUtility;
use \TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ActionViewHelper extends AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'a';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('name', 'string', 'Specifies the name of an anchor');
		$this->registerTagAttribute('rel', 'string', 'Specifies the relationship between the current document and the linked document');
		$this->registerTagAttribute('rev', 'string', 'Specifies the relationship between the linked document and the current document');
		$this->registerTagAttribute('target', 'string', 'Specifies where to open the linked document');
	}

	/**
	 * @param string $action Target action
	 * @param array $arguments Arguments
	 * @param string $controller Target controller. If NULL current controllerName is used
	 * @param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
	 * @param string $pluginName Target plugin. If empty, the current plugin name is used
	 * @param integer $pageUid target page. See TypoLink destination
	 * @param integer $pageType type of the target page. See typolink.parameter
	 * @param boolean $noCache set this to disable caching for the target page. You should not need this.
	 * @param boolean $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
	 * @param string $section the anchor to be added to the URI
	 * @param string $format The requested format, e.g. ".html
	 * @param boolean $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
	 * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	 * @param boolean $absolute If set, the URI of the rendered link is absolute
	 * @param boolean $addQueryString If set, the current query parameters will be kept in the URI
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = TRUE
	 * @param string $addQueryStringMethod Set which parameters will be kept. Only active if $addQueryString = TRUE
	 * @param boolean $forceFrontendLink Force to generate a frontend link, e.g. in backend
	 * @param integer $rootpageId Rootpage ID of page tree
	 * @return string Rendered link
	 */
	public function render($action = NULL, array $arguments = array(), $controller = NULL, $extensionName = NULL, $pluginName = NULL, $pageUid = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', $linkAccessRestrictedPages = FALSE, array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array(), $addQueryStringMethod = NULL, $forceFrontendLink = FALSE, $rootpageId = 1) {

		$uriBuilder = $this->controllerContext->getUriBuilder();

		if ($forceFrontendLink) {
			$this->initTSFE($rootpageId);
			$uri = $uriBuilder->reset()
					->setTargetPageUid($pageUid)
					->setTargetPageType($pageType)
					->setNoCache($noCache)
					->setUseCacheHash(!$noCacheHash)
					->setSection($section)
					->setFormat($format)
					->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
					->setArguments($this->uriFor($pageUid, $action, $arguments, $controller, $extensionName, $pluginName, $format, $additionalParams))
					->setCreateAbsoluteUri($absolute)
					->setAddQueryString($addQueryString)
					->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
					->setAddQueryStringMethod($addQueryStringMethod)
					->buildFrontendUri();
		} else {
			$uri = $uriBuilder->reset()
					->setTargetPageUid($pageUid)
					->setTargetPageType($pageType)
					->setNoCache($noCache)
					->setUseCacheHash(!$noCacheHash)
					->setSection($section)
					->setFormat($format)
					->setLinkAccessRestrictedPages($linkAccessRestrictedPages)
					->setArguments($additionalParams)
					->setCreateAbsoluteUri($absolute)
					->setAddQueryString($addQueryString)
					->setArgumentsToBeExcludedFromQueryString($argumentsToBeExcludedFromQueryString)
					->setAddQueryStringMethod($addQueryStringMethod)
					->uriFor($action, $arguments, $controller, $extensionName, $pluginName);
		}

		$this->tag->addAttribute('href', $uri);
		$this->tag->setContent($this->renderChildren());
		$this->tag->forceClosingTag(TRUE);
		return $this->tag->render();
	}

	/**
	 * 
	 * @param integer $targetPageUid
	 * @param string $actionName Name of the action to be called
	 * @param array $controllerArguments Additional query parameters. Will be "namespaced" and merged with $this->arguments.
	 * @param string $controllerName Name of the target controller. If not set, current ControllerName is used.
	 * @param string $extensionName Name of the target extension, without underscores. If not set, current ExtensionName is used.
	 * @param string $pluginName Name of the target plugin. If not set, current PluginName is used.
	 * @param string $format The requested format, e.g. ".html
	 * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
	 * @param string $argumentPrefix Prefix
	 * 
	 * @return array
	 */
	private function uriFor($targetPageUid = NULL, $actionName = NULL, $controllerArguments = array(), $controllerName = NULL, $extensionName = NULL, $pluginName = NULL, $format = '', array $additionalParams = array(), $argumentPrefix = NULL) {
		$environmentService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\EnvironmentService');
		$extensionService = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Service\\ExtensionService');

		if ($actionName !== NULL) {
			$controllerArguments['action'] = $actionName;
		}
		if ($controllerName !== NULL) {
			$controllerArguments['controller'] = $controllerName;
		} else {
			$controllerArguments['controller'] = $this->request->getControllerName();
		}
		if ($extensionName === NULL) {
			$extensionName = $this->request->getControllerExtensionName();
		}
		if ($pluginName === NULL && $environmentService->isEnvironmentInFrontendMode()) {
			$pluginName = $extensionService->getPluginNameByAction($extensionName, $controllerArguments['controller'], $controllerArguments['action']);
		}
		if ($pluginName === NULL) {
			$pluginName = $this->request->getPluginName();
		}
		if ($targetPageUid === NULL && $environmentService->isEnvironmentInFrontendMode()) {
			$targetPageUid = $extensionService->getTargetPidByPlugin($extensionName, $pluginName);
		}
		if ($format !== '') {
			$controllerArguments['format'] = $format;
		}
		if ($argumentPrefix !== NULL) {
			$prefixedControllerArguments = array($argumentPrefix => $controllerArguments);
		} else {
			$pluginNamespace = $extensionService->getPluginNamespace($extensionName, $pluginName);
			$prefixedControllerArguments = array($pluginNamespace => $controllerArguments);
		}
		//DebugUtility::debug(array_merge_recursive($additionalParams,$prefixedControllerArguments));
		return array_merge_recursive($additionalParams, $prefixedControllerArguments);
	}

	/**
	 * initialize the TYPO3 frontend
	 * 
	 * @param integer $id
	 * @param integer $typeNum
	 */
	protected function initTSFE($id = 1, $typeNum = 0) {
		EidUtility::initTCA();
		if (!is_object($GLOBALS['TT'])) {
			$GLOBALS['TT'] = new \TYPO3\CMS\Core\TimeTracker\NullTimeTracker;
			$GLOBALS['TT']->start();
		}
		$GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Controller\\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], $id, $typeNum);
		$GLOBALS['TSFE']->connectToDB();
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->determineId();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->getConfigArray();

		if (ExtensionManagementUtility::isLoaded('realurl')) {
			$rootline = BackendUtility::BEgetRootLine($id);
			$host = BackendUtility::firstDomainRecord($rootline);
			$_SERVER['HTTP_HOST'] = $host;
		}
	}

}
