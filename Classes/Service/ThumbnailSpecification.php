<?php
namespace TYPO3\CMS\Media\Service;

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
 * Define how a thumbnail should be rendered.
 *
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class ThumbnailSpecification {

	/**
	 * Whether the thumbnail should be wrapped with an anchor.
	 *
	 * @var bool
	 */
	protected $wrap = FALSE;

	/**
	 * Define width, height and all sort of attributes to render a thumbnail.
	 * @see TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::Image
	 *
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * DOM attributes to add to the image preview.
	 *
	 * @var array
	 */
	protected $attributes = array(
		'class' => 'thumbnail',
	);

	/**
	 * @return boolean
	 */
	public function getWrap() {
		return $this->wrap;
	}

	/**
	 * @param boolean $wrap
	 * @return \TYPO3\CMS\Media\Service\ThumbnailSpecification
	 */
	public function setWrap($wrap) {
		$this->wrap = $wrap;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * @param array $configuration
	 * @return \TYPO3\CMS\Media\Service\ThumbnailSpecification
	 */
	public function setConfiguration($configuration) {
		$this->configuration = $configuration;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * @param array $attributes
	 * @return \TYPO3\CMS\Media\Service\ThumbnailSpecification
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}
}
?>