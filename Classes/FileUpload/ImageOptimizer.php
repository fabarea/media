<?php
namespace TYPO3\CMS\Media\FileUpload;

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
 * Class that optimize an image according to some settings.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class ImageOptimizer {

	/**
	 * Optimize an image
	 *
	 * @param \TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function optimize(\TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile){
		$imageInfo = getimagesize($uploadedFile->getFileWithAbsolutePath());

		$currentWidth = $imageInfo[0];
		$currentHeight = $imageInfo[1];

		// resize an image if this one is bigger than telling by the settings
		$imageDimension = \TYPO3\CMS\Media\Utility\PresetImageDimension::getInstance()->preset('image_original');
		if ($currentWidth >= $currentHeight && $currentWidth > $imageDimension->getWidth()) {
			// resize taking the width as reference
			$width = $imageDimension->getWidth();
			$height = round($imageDimension->getWidth() * $currentHeight / $currentWidth);
			$this->resize($uploadedFile->getFileWithAbsolutePath(), $width, $height);
		} elseif ($currentHeight > $imageDimension->getHeight()) {
			// resize taking the height as reference
			$width = round($imageDimension->getHeight() * $currentWidth / $currentHeight);
			$height = $imageDimension->getHeight();
			$this->resize($uploadedFile->getFileWithAbsolutePath(), $width, $height);
		}
		return $uploadedFile;
	}

	/**
	 * Resize an image according to given parameter
	 *
	 * @throws \Exception
	 * @param string $fileNameAndPath
	 * @param int $width
	 * @param int $height
	 * @return void
	 */
	public function resize($fileNameAndPath, $width = 0, $height = 0) {
		$options = sprintf('-geometry %sx%s! -limit threads 1 -background white -extent 0x0 +matte', $width, $height);

		// We want to keep the profile on the uploaded image...
		// save current state
		$useStripProfileByDefault = $GLOBALS['TYPO3_CONF_VARS']['GFX']['im_useStripProfileByDefault'];
		$GLOBALS['TYPO3_CONF_VARS']['GFX']['im_useStripProfileByDefault'] = FALSE;
		$command = \TYPO3\CMS\Core\Utility\CommandUtility::imageMagickCommand('convert', $options . ' ' . $this->wrapFileName($fileNameAndPath) . ' ' . $this->wrapFileName($fileNameAndPath));
		exec($command, $output);

		// Reset the value
		$GLOBALS['TYPO3_CONF_VARS']['GFX']['im_useStripProfileByDefault'] = $useStripProfileByDefault;

		if (!empty($output)) {
			throw new \Exception('Resizing of image went wrong!', 1362509465);
		}
	}

	/**
	 * Escapes a file name so it can safely be used on the command line.
	 *
	 * @see \TYPO3\CMS\Core\Imaging\GraphicalFunctions
	 * @param string $inputName filename to safeguard, must not be empty
	 * @return string $inputName escaped as needed
	 */
	protected function wrapFileName($inputName) {
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) {
			$currentLocale = setlocale(LC_CTYPE, 0);
			setlocale(LC_CTYPE, $GLOBALS['TYPO3_CONF_VARS']['SYS']['systemLocale']);
		}
		$escapedInputName = escapeshellarg($inputName);
		if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['UTF8filesystem']) {
			setlocale(LC_CTYPE, $currentLocale);
		}
		return $escapedInputName;
	}

}
?>