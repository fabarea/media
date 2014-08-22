<?php
namespace TYPO3\CMS\Media\View\Button;

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
use TYPO3\CMS\Vidi\View\AbstractComponentView;
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 * View helper which renders a button for uploading assets.
 */
class UploadButton extends AbstractComponentView {

	/**
	 * Renders a button for uploading assets.
	 *
	 * @return string
	 */
	public function render() {

		/** @var $fileUpload \TYPO3\CMS\Media\Form\FileUpload */
		$fileUpload = GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\FileUpload');
		return $fileUpload->setPrefix(ModuleUtility::getParameterPrefix())->render();
	}
}
