<?php
namespace Fab\Media\ViewHelpers\Form\Select;

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

use Fab\Media\Module\MediaModule;
use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper dealing with the storage menu
 * displayed in the top right corner of the Media module.
 */
class StorageViewHelper extends AbstractViewHelper {

	/**
	 * Render a file upload field
	 *
	 * @param array $objects
	 * @return string
	 */
	public function render($objects = array()) {

		// Check if a storages is selected
		$currentStorage = $this->getMediaModule()->getCurrentStorage();

		$template = '<select name="%s[target]">%s</select>';
		$options = array();
		foreach ($objects as $storage) {

			/** @var \TYPO3\CMS\Core\Resource\ResourceStorage $storage */
			$options[] = sprintf('<option value="%s" %s>%s %s</option>',
				$storage->getUid(),
				is_object($currentStorage) && $currentStorage->getUid() == $storage->getUid() ? 'selected="selected"' : '',
				$storage->getName(),
				!$storage->isOnline() ? '(offline)' : ''
			);
		}
		return sprintf($template,
			VidiModule::getParameterPrefix(),
			implode("\n", $options)
		);
	}

	/**
	 * @return MediaModule
	 */
	protected function getMediaModule() {
		return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
	}

}
