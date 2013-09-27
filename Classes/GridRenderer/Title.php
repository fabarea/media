<?php
namespace TYPO3\CMS\Media\GridRenderer;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

/**
 * Class rendering title and description in the grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Title extends \TYPO3\CMS\Vidi\GridRenderer\GridRendererAbstract {

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
?>