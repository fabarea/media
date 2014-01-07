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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Media\Utility\StorageUtility;

/**
 * View helper which renders a dropdown menu for storage.
 */
class MenuStorageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Vidi\ModuleLoader
	 * @inject
	 */
	protected $moduleLoader;

	/**
	 * Renders a dropdown menu for storage.
	 *
	 * @return string
	 */
	public function render() {

		$storages = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorages();

		$currentStorage = StorageUtility::getInstance()->getCurrentStorage();

		/** @var $storage \TYPO3\CMS\Core\Resource\ResourceStorage */
		$options = '';
		foreach ($storages as $storage) {
			$selected = '';
			if ($currentStorage->getUid() == $storage->getUid()) {
				$selected = 'selected';
			}
			$options .= sprintf('<option value="%s" %s>%s %s</option>',
				$storage->getUid(),
				$selected,
				$storage->getName(),
				$storage->isOnline() ? '' : '(' . LocalizationUtility::translate('offline', 'media') . ')'
			);
		}

		$parameters = GeneralUtility::_GET();
		$inputs = '';
		foreach ($parameters as $parameter => $value) {
			list($parameter, $value) = $this->computeParameterAndValue($parameter, $value);
			if ($parameter !== $this->moduleLoader->getParameterPrefix() . '[storage]') {
				$inputs .= sprintf('<input type="hidden" name="%s" value="%s" />', $parameter, $value);
			}
		}

		$template = '<form action="mod.php" id="form-menu-storage" method="get">
						%s
						<select name="%s[storage]" id="menu-storage" class="btn btn-mini" onchange="$(\'#form-menu-storage\').submit()">%s</select>
					</form>';

		return sprintf($template,
			$inputs,
			$this->moduleLoader->getParameterPrefix(),
			$options
		);
	}

	/**
	 * Compute parameter and value to be correctly encoded by the browser.
	 *
	 * @param string $parameter
	 * @param mixed $value
	 * @return array
	 */
	public function computeParameterAndValue($parameter, $value){

		if (is_string($value)) {
			$result = array($parameter, $value);
		} else {
			$key = key($value);
			$value = current($value);
			$parameter =  sprintf('%s[%s]', $parameter, $key);
			$result = $this->computeParameterAndValue($parameter, $value);
		}
		return $result;
	}
}

?>