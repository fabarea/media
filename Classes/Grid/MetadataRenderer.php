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

use Fab\Vidi\Tca\FieldType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\ObjectFactory;
use TYPO3\CMS\Vidi\Grid\GridRendererAbstract;
use Fab\Vidi\Tca\Tca;

/**
 * Class for rendering a configurable metadata property of a file in the Grid.
 */
class MetadataRenderer extends GridRendererAbstract {

	/**
	 * Renders a configurable metadata property of a file in the Grid.
	 *
	 * @throws \Exception
	 * @return string
	 */
	public function render() {

		if (empty($this->gridRendererConfiguration['property'])) {
			throw new \Exception('Missing property value for Grid Renderer Metadata', 1390391042);
		}

		$propertyName = $this->gridRendererConfiguration['property'];
		$file = $this->getFileConverter()->convert($this->object);
		$result = $file->getProperty($propertyName);

		// Avoid bad surprise, converts characters to HTML.
		$fieldType = Tca::table('sys_file_metadata')->field($propertyName)->getType();
		if ($fieldType !== FieldType::TEXTAREA) {
			$result = htmlentities($result);
		} elseif ($fieldType === FieldType::TEXTAREA && !$this->isClean($result)) {
			$result = htmlentities($result);
		} elseif ($fieldType === FieldType::TEXTAREA && !$this->hasHtml($result)) {
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

	/**
	 * @return \Fab\Media\TypeConverter\ContentToFileConverter
	 */
	protected function getFileConverter() {
		return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
	}
}
