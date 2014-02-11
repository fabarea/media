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
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Vidi\Grid\GridRendererAbstract;
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * Class rendering the preview of a media in the grid
 */
class MetadataRenderer extends GridRendererAbstract {

	/**
	 * Render a preview of an media.
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function render() {

		if (empty($this->gridRendererConfiguration['property'])) {
			throw new \Exception('Missing property value for Grid Renderer Metadata', 1390391042);
		}

		$propertyName = $this->gridRendererConfiguration['property'];
		$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($this->object);
		$result = $asset->getProperty($propertyName);

		// Avoid bad surprise, converts characters to HTML.
		$fieldType = TcaService::table('sys_file_metadata')->field($propertyName)->getFieldType();
		if ($fieldType !== TcaService::TEXTAREA) {
			$result = htmlentities($result);
		} elseif ($fieldType === TcaService::TEXTAREA && !$this->isClean($result)) {
			$result = htmlentities($result);
		} elseif ($fieldType === TcaService::TEXTAREA && !$this->hasHtml($result)) {
			$result = nl2br($result);
		}

		return $result;
	}

	/**
	 * Check whether a string contains HTML tags.
	 *
	 * @param string $content the content to be analyzed
	 * @return boolean
	 */
	protected function hasHtml($content) {
		$result = FALSE;

		// We compare the length of the string with html tags and without html tags.
		if (strlen($content) != strlen(strip_tags($content))) {
			$result = TRUE;
		}
		return $result;
	}

	/**
	 * Check whether a string contains potential XSS
	 *
	 * @param string $content the content to be analyzed
	 * @return boolean
	 */
	protected function isClean($content) {

		// @todo implement me!
		$result = TRUE;
		return $result;
	}
}
