<?php
namespace TYPO3\CMS\Media\ViewHelpers\File;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns property value of a file given by the context.
 */
class PropertyViewHelper extends AbstractViewHelper {

	/**
	 * Returns a property value of a file given by the context.
	 *
	 * @param string $name
	 * @return string
	 */
	public function render($name) {

		/** @var File $file */
		$file = $this->templateVariableContainer->get('file');
		return $file->getProperty($name);
	}
}
