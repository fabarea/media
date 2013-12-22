<?php
namespace TYPO3\CMS\Media\FileUpload\Optimizer;
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
use TYPO3\CMS\Media\FileUpload\ImageOptimizerInterface;

/**
 * Class that optimize an image according to some settings.
 */
class Rotate implements ImageOptimizerInterface {

	/**
	 * @var \TYPO3\CMS\Frontend\Imaging\GifBuilder
	 */
	protected $gifCreator;

	/**
	 * @return \TYPO3\CMS\Media\FileUpload\Optimizer\Rotate
	 */
	public function __construct() {
		$this->gifCreator = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Imaging\\GifBuilder');
		$this->gifCreator->init();
		$this->gifCreator->absPrefix = PATH_site;
	}
	/**
	 * Optimize the given uploaded image
	 *
	 * @param \TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function optimize($uploadedFile) {

		$orientation = $this->getOrientation($uploadedFile->getFileWithAbsolutePath());
		$isRotated = $this->isRotated($orientation);

		// Only rotate image if necessary!
		if ($isRotated > 0) {
			$transformation = $this->getTransformation($orientation);

			$imParams = '###SkipStripProfile###';
			if ($transformation !== '') {
				$imParams .= ' ' . $transformation;
			}

			$tempFileInfo = $this->gifCreator->imageMagickConvert($uploadedFile->getFileWithAbsolutePath(), '', '', '', $imParams, '', array(), TRUE);
			if ($tempFileInfo) {
				// Replace original file
				@unlink($uploadedFile->getFileWithAbsolutePath());
				@rename($tempFileInfo[3], $uploadedFile->getFileWithAbsolutePath());

				if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['im_version_5'] === 'gm') {
					$this->resetOrientation($uploadedFile->getFileWithAbsolutePath());
				}
			}
		}
		return $uploadedFile;
	}

	/**
	 * Returns the EXIF orientation of a given picture.
	 *
	 * @param string $filename
	 * @return integer
	 */
	protected function getOrientation($filename) {
		$extension = strtolower(substr($filename, strrpos($filename, '.') + 1));
		$orientation = 1; // Fallback to "straight"
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::inList('jpg,jpeg,tif,tiff', $extension) && function_exists('exif_read_data')) {
			$exif = exif_read_data($filename);
			if ($exif) {
				$orientation = $exif['Orientation'];
			}
		}
		return $orientation;
	}

	/**
	 * Returns TRUE if the given picture is rotated.
	 *
	 * @param integer $orientation EXIF orientation
	 * @return integer
	 * @see http://www.impulseadventure.com/photo/exif-orientation.html
	 */
	protected function isRotated($orientation) {
		$ret = FALSE;
		switch ($orientation) {
			case 2: // horizontal flip
			case 3: // 180°
			case 4: // vertical flip
			case 5: // vertical flip + 90 rotate right
			case 6: // 90° rotate right
			case 7: // horizontal flip + 90 rotate right
			case 8: // 90° rotate left
				$ret = TRUE;
				break;
		}
		return $ret;
	}

	/**
	 * Returns a command line parameter to fix the orientation of a rotated picture.
	 *
	 * @param integer $orientation
	 * @return string
	 */
	protected function getTransformation($orientation) {
		$transformation = '';
		if ($GLOBALS['TYPO3_CONF_VARS']['GFX']['im_version_5'] !== 'gm') {
			// ImageMagick
			if ($orientation >= 2 && $orientation <= 8) {
				$transformation = '-auto-orient';
			}
		} else {
			// GraphicsMagick
			switch ($orientation) {
				case 2: // horizontal flip
					$transformation = '-flip horizontal';
					break;
				case 3: // 180°
					$transformation = '-rotate 180';
					break;
				case 4: // vertical flip
					$transformation = '-flip vertical';
					break;
				case 5: // vertical flip + 90 rotate right
					$transformation = '-transpose';
					break;
				case 6: // 90° rotate right
					$transformation = '-rotate 90';
					break;
				case 7: // horizontal flip + 90 rotate right
					$transformation = '-transverse';
					break;
				case 8: // 90° rotate left
					$transformation = '-rotate 270';
					break;
			}
		}
		return $transformation;
	}

	/**
	 * Resets the EXIF orientation flag of a picture.
	 *
	 * @param string $filename
	 * @return void
	 * @see http://sylvana.net/jpegcrop/exif_orientation.html
	 */
	protected function resetOrientation($filename) {
		JpegExifOrient::setOrientation($filename, 1);
	}

}
?>