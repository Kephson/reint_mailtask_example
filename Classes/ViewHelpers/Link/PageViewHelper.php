<?php

namespace RENOLIT\ReintMailtaskExample\ViewHelpers\Link;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * A ViewHelper for creating links to TYPO3 pages.
 *
 * Examples
 * ========
 *
 * Link to the current page
 * ------------------------
 *
 * ::
 *
 *    <f:link.page>page link</f:link.page>
 *
 * Output::
 *
 *    <a href="/page/path/name.html">page link</a>
 *
 * Depending on current page, routing and page path configuration.
 *
 * Query parameters
 * ----------------
 *
 * ::
 *
 *    <f:link.page pageUid="1" additionalParams="{foo: 'bar'}">page link</f:link.page>
 *
 * Output::
 *
 *    <a href="/page/path/name.html?foo=bar">page link</a>
 *
 * Depending on current page, routing and page path configuration.
 *
 * Query parameters for extensions
 * -------------------------------
 *
 * ::
 *
 *    <f:link.page pageUid="1" additionalParams="{extension_key: {foo: 'bar'}}">page link</f:link.page>
 *
 * Output::
 *
 *    <a href="/page/path/name.html?extension_key[foo]=bar">page link</a>
 *
 * Depending on current page, routing and page path configuration.
 */
class PageViewHelper extends AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * Arguments initialization
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination', true);
        $this->registerArgument('arguments', 'array', 'Additional arguments for the URI');
    }

    /**
     * @return string Rendered page URI
     * @throws SiteNotFoundException
     */
    public function render(): string
    {
        $pageUid = (int)$this->arguments['pageUid'];
        $arguments = isset($this->arguments['arguments']) ? (array)$this->arguments['arguments'] : [];
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId($pageUid);
        $uri = (string)$site->getRouter()->generateUri($pageUid, $arguments);

        if ($uri !== '') {
            $this->tag->addAttribute('href', $uri);
            $this->tag->setContent($this->renderChildren());
            $this->tag->forceClosingTag(true);
            $result = $this->tag->render();
        } else {
            $result = $this->renderChildren();
        }
        return $result;
    }
}
