<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which renders a dropdown menu for storage.
 */
class MenuStorageViewHelper extends AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Vidi\Module\ModuleLoader
	 * @inject
	 */
	protected $moduleLoader;

	/**
	 * Renders a dropdown menu for storage.
	 *
	 * @return string
	 */
	public function render() {

		$currentStorage = $this->getStorageService()->findCurrentStorage();

		/** @var $storage \TYPO3\CMS\Core\Resource\ResourceStorage */
		$options = '';
		foreach ($this->getStorageService()->findByBackendUser() as $storage) {
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
						<select name="%s[storage]" class="btn btn-min" id="menu-storage" onchange="$(\'#form-menu-storage\').submit()">%s</select>
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

	/**
	 * @return \TYPO3\CMS\Media\Resource\StorageService
	 */
	protected function getStorageService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\StorageService');
	}
}
