<?php
namespace Fab\Media\Grid;

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

use Fab\Vidi\Grid\ColumnRendererAbstract;

/**
 * Class rendering category list of an asset in the grid.
 */
class CategoryRenderer extends ColumnRendererAbstract {

	/**
	 * Renders category list of an asset in the grid.
	 *
	 * @return string
	 */
	public function render() {
		$result = '';

		$categories = $this->object['metadata']['categories'];
		if (!empty($categories)) {

			/** @var $category \TYPO3\CMS\Extbase\Domain\Model\Category */
			foreach ($categories as $category) {
				$result .= sprintf('<li>%s</li>', $category['title']);
			}
			$result = sprintf('<ul class="category-list">%s</ul>', $result);
		}
		return $result;
	}

}
