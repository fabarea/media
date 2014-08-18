<?php
namespace TYPO3\CMS\Media\Grid;
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Vidi\Grid\GridRendererAbstract;

/**
 * Class rendering category list of an asset in the grid.
 */
class CategoryRenderer extends GridRendererAbstract {

	/**
	 * Renders category list of an asset in the grid.
	 *
	 * @return string
	 */
	public function render() {
		$result = '';

		$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($this->object);
		$categories = $this->getFileService()->findCategories($asset);

		if (!empty($categories)) {

			/** @var $category \TYPO3\CMS\Extbase\Domain\Model\Category */
			foreach ($categories as $category) {
				$result .= sprintf('<li>%s</li>', $category['title']);
			}
			$result = sprintf('<ul class="category-list">%s</ul>', $result);
		}
		return $result;
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\FileService
	 */
	protected function getFileService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\FileService');
	}
}
