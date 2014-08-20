<?php
namespace TYPO3\CMS\Media\ViewHelpers;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which can output metadata of an asset in a flexible way.
 * Give a input a template + set of metadata properties to render, example:
 *
 * $template = '%width x %height';
 * $fileProperties = array('width', 'height');
 */
class MetadataViewHelper extends AbstractViewHelper {

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

		if (empty($template)) {
			$template = $this->getDefaultTemplate($file);
		}

		$result = $template;
		foreach ($metadataProperties as $metadataProperty) {
			$value = $file->getProperty($metadataProperty);
			if ($metadataProperty === 'size') {
				$sizeUnit = empty($configuration['sizeUnit']) ? 1000 : $configuration['sizeUnit'];
				$value = round($file->getSize() / $sizeUnit);
			}
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
