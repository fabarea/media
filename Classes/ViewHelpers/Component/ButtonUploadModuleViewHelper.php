<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 * View helper which renders a button for uploading assets.
 */
class ButtonUploadModuleViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $extensionName = 'media';

	/**
	 * Renders a button for uploading assets.
	 *
	 * @return string
	 */
	public function render() {

		$callBack = <<< EOF

			// Callback action after file upload
			if (responseJSON.uid) {

				// Hide message for file upload
				$('.qq-upload-list', this).find('li:eq(' + id + ')').fadeOut(500);

				// Reset table only if all files have been uploaded
				if ($('.qq-upload-list', this).find('li').not('.alert-success').length == 0) {
					Vidi.table.fnResetDisplay();
				}
			}
EOF;

		/** @var $fileUpload \TYPO3\CMS\Media\Form\FileUpload */
		$fileUpload = GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\FileUpload');
		$fileUpload->setPrefix(ModuleUtility::getParameterPrefix())->setCallBack($callBack);
		return $fileUpload->render();
	}
}

?>