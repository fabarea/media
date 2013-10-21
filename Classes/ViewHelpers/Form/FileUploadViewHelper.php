<?php
namespace TYPO3\CMS\Media\ViewHelpers\Form;

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
use TYPO3\CMS\Media\Utility\ModuleUtility;

/**
 * View helper dealing with file upload widget.
 */
class FileUploadViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Render a file upload field
	 *
	 * @return string
	 */
	public function render() {

		$searches[] = '<script type="text/javascript">';
		$searches[] = '</script>';
		$callBack = str_replace($searches, '', $this->renderChildren());

		/** @var $fileUpload \TYPO3\CMS\Media\Form\FileUpload */
		$fileUpload = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\FileUpload');
		$fileUpload->setPrefix(ModuleUtility::getParameterPrefix())->setCallBack($callBack);
		return $fileUpload->render();
	}
}

?>