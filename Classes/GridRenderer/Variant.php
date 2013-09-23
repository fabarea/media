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
 * Class rendering usage for the Grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Variant implements \TYPO3\CMS\Media\GridRenderer\GridRendererInterface {

	/**
	 * Render a categories for a media
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $asset = NULL) {

		$result = $_result = '';

		// Get the file variants
		$variants = $asset->getVariants();
		if (!empty($variants)) {

			$_template = <<<EOF
<li title="uid: %s">
	<a href="%s" class="%s" target="_blank" data-original-uid="%s" data-file-uid="%s" data-public-url="%s" data-time-stamp="%s">%s</a> %s x %s
</li>
EOF;
			// Computes sprite icon.
			$parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET('tx_media_user_mediam1');
			$icon =  isset($parameters['rtePlugin']) ?
				\TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('extensions-media-variant-link') :
				\TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('extensions-media-variant');

			// Compiles templates for each variants.
			foreach ($variants as $variant) {
				$_result .= sprintf($_template,
					$variant->getVariant()->getUid(),
					\TYPO3\CMS\Media\Utility\Path::getRelativePath($variant->getVariant()->getPublicUrl()),
					isset($parameters['rtePlugin']) ? 'btn-variant-link' : 'btn-variant',
					$variant->getOriginal()->getUid(),
					$variant->getVariant()->getUid(),
					$variant->getVariant()->getPublicUrl(),
					$GLOBALS['_SERVER']['REQUEST_TIME'],
					$icon,
					$variant->getVariant()->getProperty('width'),
					$variant->getVariant()->getProperty('height')
				);
			}

			// finalize reference assembling
			$_template = '<span style="text-decoration: underline">%s (%s)</span><ul style="margin: 0 0 10px 0">%s</ul>';
			$result = sprintf($_template,
				\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('variants', 'media'),
				count($variants),
				$_result
			);
		}
		return $result;
	}
}
?>