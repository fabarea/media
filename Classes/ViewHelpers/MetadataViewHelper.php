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
 * $template = '%width x %height';
 * $fileProperties = array('width', 'height');
 */
class MetadataViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Returns metadata according to a template.
	 *
	 * @param File $file
	 * @param string $template
	 * @param array $metadataProperties
	 * @param array $configuration
	 * @return string
	 */
	public function render(File $file, $template = '', array $metadataProperties = array('size', 'width', 'height'), $configuration = array()) {

		$values = array();
		foreach ($metadataProperties as $metadataProperty) {
			$value = $file->getProperty($metadataProperty);
			if ($metadataProperty === 'size') {
				$sizeUnit = empty($configuration['sizeUnit']) ? 1000 : $configuration['sizeUnit'];
				$value = round($file->getSize() / $sizeUnit);
			}
			$values[$metadataProperty] = $value;
		}

		if (empty($template)) {
			$template = $this->getDefaultTemplate($file);
		}

		$result = $template;
		foreach ($values as $metadataProperty => $value) {
			$result = str_replace('%' . $metadataProperty, $value, $result);
		}

		return $result;
	}

	/**
	 * Returns a default template.
	 *
	 * @param File $file
	 * @return string
	 */
	protected function getDefaultTemplate(File $file){

		$template = '%size Ko';

		if ($file->getType() == File::FILETYPE_IMAGE) {
			$template = '%width x %height - ' . $template;
		}

		return $template;
	}
}
