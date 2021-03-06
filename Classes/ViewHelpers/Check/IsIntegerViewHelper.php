<?php
namespace RENOLIT\ReintMailtaskExample\ViewHelpers\Check;

/**
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

/**
 * ViewHelper to check if a variable is an integer and greater than 0
 *
 * # Example: Basic example
 * <code>
 * {r:check.isInteger(variable:'{var}')}
 * </code>
 * <output>
 * TRUE or FALSE
 * </output>
 *
 * @package TYPO3
 * @subpackage reint_mailtask_example
 */
class IsIntegerViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * checks if a variable is an integer
	 *
	 * @param mixed $variable
	 * @return boolean
	 */
	public function render($variable) {
		if ((int) $variable > 0) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

}
