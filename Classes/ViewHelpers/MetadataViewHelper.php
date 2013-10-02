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
/**
 * View helper which can output metadata of an asset in a flexible way.
 */
class MetadataViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $template = '<div class="metadata">%s</div>';

	/**
	 * Returns formatted metadata
	 *
	 * @param object $object
	 * @param string $format
	 * @param array $properties
	 * @param string $template
	 * @param array $configuration
	 * @return string
	 */
	public function render($object, $format, array $properties, $template = NULL, $configuration = array()) {

		$propertyValues = array();
		foreach ($properties as $propertyName) {
			$value = $object->getProperty($propertyName);
			if ($propertyName === 'size') {
				$sizeUnit = empty($configuration['sizeUnit']) ? 1000 : $configuration['sizeUnit'];
				$value = round($object->getSize() / $sizeUnit);
			}
			$propertyValues[] = $value;
		}

		if (! is_null($template)) {
			$this->template = $template;
		}

		$stringToFormat = sprintf($this->template, $format);
		return vsprintf($stringToFormat, $propertyValues);
	}
}

?>