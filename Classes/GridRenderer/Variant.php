<?php
namespace TYPO3\CMS\Media\GridRenderer;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Utility\Path;
use TYPO3\CMS\Vidi\GridRenderer\GridRendererAbstract;
use TYPO3\CMS\Vidi\ModulePlugin;

/**
 * Class rendering variants of assets in the grid.
 */
class Variant extends GridRendererAbstract {

	/**
	 * Render a variants list for a media.
	 *
	 * @return string
	 */
	public function render() {

		$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($this->object);

		$result = $_result = '';

		// Get the file variants
		$variants = $asset->getVariants();
		if (!empty($variants)) {

			// Computes sprite icon.
			$icon = ModulePlugin::getInstance()->isPluginRequired('imageEditor') ?
				IconUtility::getSpriteIcon('extensions-media-variant-link') :
				IconUtility::getSpriteIcon('extensions-media-variant');

			// Compiles templates for each variants.
			foreach ($variants as $variant) {

				// Count usage
				$softImageReferencesCount = $this->countSoftImageReferences($variant->getVariant());

				$_result .= sprintf($this->getVariantTemplate(),
					$variant->getVariant()->getUid(),
					$softImageReferencesCount,
					$softImageReferencesCount > 1 ? 's' : '',
					Path::getRelativePath($variant->getVariant()->getPublicUrl()),
					ModulePlugin::getInstance()->isPluginRequired('imageEditor') ? 'btn-variant-link' : 'btn-variant',
					$variant->getOriginal()->getUid(),
					$variant->getVariant()->getUid(),
					$variant->getVariant()->getPublicUrl(),
					$GLOBALS['_SERVER']['REQUEST_TIME'],
					$softImageReferencesCount > 0 ? '' : 'opacity: 0.6',
					$icon,
					$variant->getVariant()->getProperty('width'),
					$variant->getVariant()->getProperty('height')
				);
			}

			// finalize variant assembling
			$result = sprintf('<ul style="margin: 0 0 10px 0">%s</ul>', $_result);
		}
		return $result;
	}

	/**
	 * Return number of usages.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	public function countSoftImageReferences($asset) {

		// Get the file references of the asset.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTcountRows(
			'recuid',
			'sys_refindex',
			'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $asset->getUid()
		);
		return $softReferences;
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Return HTML template for Variant case.
	 *
	 * @return string
	 */
	protected function getVariantTemplate() {
		return '<li title="%s - %s usage%s"><a href="%s" class="%s" target="_blank"
			data-original-uid="%s" data-file-uid="%s" data-public-url="%s" data-time-stamp="%s" style="%s">%s</a> %s x %s</li>';
	}


}
?>