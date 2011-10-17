<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 
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
 *
 *
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Media_Domain_Model_Text extends Tx_Media_Domain_Model_Media {

	/**
	 * Width
	 *
	 * @var int
	 */
	protected $width;
	
	/**
	 * Height
	 *
	 * @var int
	 */
	protected $height;
	
	/**
	 * Unit
	 *
	 * @var int
	 */
	protected $unit;

	/**
	 * Pages
	 *
	 * @var int
	 */
	protected $pages;
	
	/**
	 * Language
	 *
	 * @var string
	 */
	protected $language;
	
	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {
		//Do not remove the next line: It would break the functionality
		$this->initStorageObjects();
	}
	
	/**
	 * Returns the width
	 *
	 * @return string $width
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * Sets the width
	 *
	 * @param string $width
	 * @return void
	 */
	public function setWidth($width) {
		$this->width = $width;
	}
	
	/**
	 * Returns the height
	 *
	 * @return string $height
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * Sets the height
	 *
	 * @param string $height
	 * @return void
	 */
	public function setHeight($height) {
		$this->height = $height;
	}

	/**
	 * Returns the unit
	 *
	 * @return string $unit
	 */
	public function getUnit() {
		return $this->unit;
	}

	/**
	 * Sets the unit
	 *
	 * @param string $unit
	 * @return void
	 */
	public function setUnit($unit) {
		$this->unit = $unit;
	}
	
	/**
	 * Returns the pages
	 *
	 * @return string $pages
	 */
	public function getPages() {
		return $this->pages;
	}

	/**
	 * Sets the pages
	 *
	 * @param string $pages
	 * @return void
	 */
	public function setPages($pages) {
		$this->pages = $pages;
	}
	
	/**
	 * Returns the language
	 *
	 * @return string $language
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * Sets the language
	 *
	 * @param string $language
	 * @return void
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}
}
?>