<?php
namespace TYPO3\CMS\Media\ViewHelpers;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
use TYPO3\CMS\Core\Resource\File;

/**
 * View helper which can output metadata of an asset in a flexible way.
 * Give a input a template + set of metadata properties to render, example:
 *
 * $template = '%s x %s';
 * $fileProperties = array('width', 'height');
 */
class MetadataViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $wrapper = '<div class="fileInfo" style="font-size: 7pt; color: #777;">%s</div>';

	/**
	 * Returns metadata according to a template.
	 *
	 * @param File $file
	 * @param string $template
	 * @param array $metadataProperties
	 * @param string $wrapper
	 * @param array $configuration
	 * @return string
	 */
	public function render(File $file, $template, array $metadataProperties, $wrapper = '', $configuration = array()) {

		$values = array();
		foreach ($metadataProperties as $metadataProperty) {
			$value = $file->getProperty($metadataProperty);
			if ($metadataProperty === 'size') {
				$sizeUnit = empty($configuration['sizeUnit']) ? 1000 : $configuration['sizeUnit'];
				$value = round($file->getSize() / $sizeUnit);
			}
			$values[] = $value;
		}

		if (!empty($wrapper)) {
			$this->wrapper = $wrapper;
		}

		$wrappedTemplate = sprintf($this->wrapper, $template);
		return vsprintf($wrappedTemplate, $values);
	}
}
