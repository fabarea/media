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
 * Class rendering title and description for the Grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Title implements \TYPO3\CMS\Media\GridRenderer\GridRendererInterface {

	/**
	 * Render title for the Grid.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $asset = NULL) {

		$result = '';
		$template = '<div>%s %s <br /><span class="text-light">%s</span></div>';

		if ($asset->getTitle() || $asset->getDescription()) {

			// Get a possible default icon
			$defaultFlag = '';
			$tsConfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getModTSconfig(0, 'mod.SHARED');
			// fallback non sprite-configuration
			if (($pos = strrpos($tsConfig['properties']['defaultLanguageFlag'], '.')) !== FALSE) {
				$defaultFlag = substr($tsConfig['properties']['defaultLanguageFlag'], 0, $pos);
			}

			$result = sprintf($template,
				empty($defaultFlag) ? '' : \TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('flags-' . $defaultFlag),
				$asset->getTitle(),
				$asset->getDescription() // @todo shorten text if too long
			);
		}

		// Get the Language Uid checking whether to render flags
		$languages = \TYPO3\CMS\Media\Utility\Language::getInstance()->getLanguages();
		if (!empty($languages) && $asset->getUid() > 0) {

			foreach ($languages as $language) {
				$records = \TYPO3\CMS\Media\Utility\Overlays::getOverlayRecords('sys_file', array($asset->getUid()), $language['uid']);

				if (!empty($records[$asset->getUid()])) {
					$key = key($records[$asset->getUid()]);
					$record = $records[$asset->getUid()][$key];

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