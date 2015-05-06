<?php
namespace Fab\Media\ViewHelpers\File;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * View helper which returns the File Uri.
 */
class UriViewHelper extends AbstractViewHelper {

	/**
	 * Returns a property value of a file given by the context.
	 *
	 * @param File|Content|int $file
	 * @param bool $relative
	 * @return string
	 */
	public function render($file, $relative = FALSE) {
		if (! $file instanceof File) {
			$file = $this->getFileConverter()->convert($file);
		}
		return $file->getPublicUrl($relative);
	}

	/**
	 * @return \Fab\Media\TypeConverter\ContentToFileConverter
	 */
	protected function getFileConverter() {
		return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
	}
}
