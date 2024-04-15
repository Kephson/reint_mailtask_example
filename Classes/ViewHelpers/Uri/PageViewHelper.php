<?php

namespace RENOLIT\ReintMailtaskExample\ViewHelpers\Uri;

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

use Closure;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

/**
 * A ViewHelper for creating URIs to TYPO3 pages.
 *
 * Examples
 * ========
 *
 * URI to the current page
 * -----------------------
 *
 * ::
 *
 *    <f:uri.page>page link</f:uri.page>
 *
 * ``/page/path/name.html``
 *
 * Depending on current page, routing and page path configuration.
 *
 * Query parameters
 * ----------------
 *
 * ::
 *
 *    <f:uri.page pageUid="1" additionalParams="{foo: 'bar'}" />
 *
 * ``/page/path/name.html?foo=bar``
 *
 * Depending on current page, routing and page path configuration.
 *
 * Query parameters for extensions
 * -------------------------------
 *
 * ::
 *
 *    <f:uri.page pageUid="1" additionalParams="{extension_key: {foo: 'bar'}}" />
 *
 * ``/page/path/name.html?extension_key[foo]=bar``
 *
 * Depending on current page, routing and page path configuration.
 */
class PageViewHelper extends AbstractViewHelper
{

    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        $this->registerArgument('pageUid', 'int', 'target PID', true);
        $this->registerArgument('arguments', 'array', 'Additional arguments for the URI');
        $this->registerArgument('additionalParams', 'array', 'query parameters to be attached to the resulting URI', false, []);
    }

    /**
     * @param array $arguments
     * @param Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string Rendered page URI
     * @throws SiteNotFoundException
     */
    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    )
    {
        $pageUid = (int)$arguments['pageUid'];
        $arguments = isset($arguments['arguments']) ? (array)$arguments['arguments'] : [];
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $site = $siteFinder->getSiteByPageId((int)$pageUid);
        return (string)$site->getRouter()->generateUri($pageUid, $arguments);
    }
}
