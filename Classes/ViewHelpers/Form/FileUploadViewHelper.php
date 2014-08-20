<?php
namespace TYPO3\CMS\Media\ViewHelpers\Form;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 * View helper dealing with file upload widget.
 */
class FileUploadViewHelper extends AbstractViewHelper {

	/**
	 * Render a file upload field
	 *
	 * @return string
	 */
	public function render() {

		/** @var $fileUpload \TYPO3\CMS\Media\Form\FileUpload */
		$fileUpload = GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\FileUpload');
		$fileUpload->setPrefix(ModuleUtility::getParameterPrefix());
		return $fileUpload->render();
	}
}
