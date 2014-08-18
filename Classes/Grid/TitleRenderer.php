<?php
namespace TYPO3\CMS\Media\Grid;

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
 * Class rendering title and description in the grid.
 */
class TitleRenderer extends \TYPO3\CMS\Vidi\Grid\GridRendererAbstract {

	/**
	 * Render title in the grid.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';
		$template = '<div>%s %s <br /><span class="text-light">%s</span></div>';

		if ($this->object->getTitle() || $this->object->getDescription()) {

			// Get a possible default icon
			$defaultFlag = '';
			// @todo fix performance
//			$tsConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getModTSconfig(0, 'mod.SHARED');
//			// fallback non sprite-configuration
//			if (($pos = strrpos($tsConfig['properties']['defaultLanguageFlag'], '.')) !== FALSE) {
//				$defaultFlag = substr($tsConfig['properties']['defaultLanguageFlag'], 0, $pos);
//			}
			$defaultFlag = '';
			$result = sprintf($template,
				empty($defaultFlag) ? '' : \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('flags-' . $defaultFlag),
				$this->object->getTitle(),
				$this->object->getDescription() // @todo shorten text if too long
			);
		}

		// Get the Language Uid checking whether to render flags
		$languages = \TYPO3\CMS\Media\Utility\Language::getInstance()->getLanguages();
		if (!empty($languages) && $this->object->getUid() > 0) {

			foreach ($languages as $language) {
				$records = \TYPO3\CMS\Media\Utility\Overlays::getOverlayRecords('sys_file', array($this->object->getUid()), $language['uid']);

				if (!empty($records[$this->object->getUid()])) {
					$key = key($records[$this->object->getUid()]);
					$record = $records[$this->object->getUid()][$key];

					$result .= sprintf($template,
						\TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('flags-' . $language['flag']),
						$record['title'],
						$record['description']
					);
				}
			}
		}

		return $result;
	}
}
