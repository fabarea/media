<?php
namespace TYPO3\CMS\Media\Renderer\Grid;
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
 * Class rendering the preview of a media in the grid
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Usage implements \TYPO3\CMS\Media\Renderer\RendererInterface {

	/**
	 * Render a categories for a media
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $asset = NULL) {

		$result = '';
		// we are force to convert to array. Method isValid behaves as a singleton.
		$variants = $asset->getVariants();

		if (! empty($variants)) {
			foreach ($variants as $variant) {
				$uids[] = $variant->getUid();
			}

			$template = '<ul style="list-style: disc"><li title="%s">%s variant%s</li></ul>';
			$result .= sprintf($template,
				'file uid: ' . implode(', ', $uids),
				count($variants),
				count($variants) > 1 ? 's' : ''
			);
		}
		return $result;
	}
}
?>