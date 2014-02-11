<?php
namespace TYPO3\CMS\Media\Service;

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

/**
 * Thumbnail Interface
 */
interface ThumbnailInterface {

	/**
	 * The thumbnail of the asset (default value).
	 */
	const OUTPUT_IMAGE = 'image';

	/**
	 * The thumbnail will be wrapped with an anchor.
	 */
	const OUTPUT_IMAGE_WRAPPED = 'imageWrapped';

	/**
	 * Output the URI of the thumbnail.
	 */
	const OUTPUT_URI = 'uri';

	/**
	 * Open thumbnail in a new window.
	 */
	const TARGET_BLANK = '_blank';

	/**
	 * Open thumbnail in the same window.
	 */
	const TARGET_SELF = '_self';

	/**
	 * Render a thumbnail of an image.
	 *
	 * @return string
	 */
	public function create();

	/**
	 * Tell to wrap the thumbnail or not.
	 *
	 * @param bool $wrap
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 * @deprecated will be removed in Media 1.2
	 */
	public function doWrap($wrap = TRUE);

	/**
	 * Tell whether the thumbnail is wrapped within an anchor tag.
	 *
	 * @return bool
	 * @deprecated will be removed in Media 1.2
	 */
	public function isWrapped();

	/**
	 * @return object
	 */
	public function getFile();

	/**
	 * @param object $file
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setFile($file);

	/**
	 * @return array
	 */
	public function getConfiguration();

	/**
	 * @param array $configuration
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setConfiguration($configuration);

	/**
	 * Return the configuration for the anchor file
	 * which is wrapping the image tag.
	 *
	 * @return array
	 */
	public function getConfigurationWrap();

	/**
	 * Define all sort of configuration for the anchor file
	 * which is wrapping the image tag.
	 *
	 * @param array $configurationWrap
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setConfigurationWrap($configurationWrap);

	/**
	 * @return array
	 */
	public function getAttributes();

	/**
	 * @param array $attributes
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setAttributes($attributes);

	/**
	 * @return string
	 */
	public function getOutputType();

	/**
	 * @param string $outputType
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setOutputType($outputType);

	/**
	 * @return string
	 */
	public function getTarget();

	/**
	 * @param string $target
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setTarget($target);

	/**
	 * @return string
	 */
	public function getAnchorUri();

	/**
	 * @param string $uri
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setAnchorUri($uri);

	/**
	 * @return boolean
	 */
	public function getAppendTimeStamp();

	/**
	 * @param boolean $appendTimeStamp
	 * @return \TYPO3\CMS\Media\Service\ThumbnailInterface
	 */
	public function setAppendTimeStamp($appendTimeStamp);
}
