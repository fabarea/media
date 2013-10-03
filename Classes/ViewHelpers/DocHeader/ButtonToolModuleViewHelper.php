<?php
namespace TYPO3\CMS\Media\ViewHelpers\DocHeader;
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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View helper which renders a dropdown menu for storage.
 */
class ButtonToolModuleViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $extensionName = 'media';

	/**
	 * @var \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected $backendUser;

	/**
	 *
	 */
	public function __construct(){
		$this->backendUser = $GLOBALS['BE_USER'];
	}

	/**
	 * Renders a dropdown menu for storage.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';
		if ($this->backendUser->isAdmin()) {

			/** @var \TYPO3\CMS\Vidi\ViewHelpers\Uri\AjaxDispatcherViewHelper $ajaxDispatcherViewHelper */
			$ajaxDispatcherViewHelper = $this->objectManager->get('TYPO3\CMS\Vidi\ViewHelpers\Uri\AjaxDispatcherViewHelper');

			$result = sprintf('<div class="pull-right"><a href="%s" class="btn btn-mini btn-doc-header"><span class="icon-cog"></span></a></div>',
				$ajaxDispatcherViewHelper->render($this->extensionName, 'Asset', 'upload')
			);
		}
		return $result;
	}
}

?>