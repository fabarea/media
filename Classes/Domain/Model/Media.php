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
class Tx_Media_Domain_Model_Media extends t3lib_file_Domain_Model_File {

	/**
	 * Title
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * Description
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Keywords
	 *
	 * @var string
	 */
	protected $keywords;

	/**
	 * MIME type
	 *
	 * @var string
	 */
	protected $mimeType;

	/**
	 * File extension
	 *
	 * @var string
	 */
	protected $extension;

	/**
	 * File creation date
	 *
	 * @var DateTime
	 */
	protected $creationDate;

	/**
	 * File modification date
	 *
	 * @var DateTime
	 */
	protected $modificationDate;

	/**
	 * Creator tool
	 *
	 * @var string
	 */
	protected $creatorTool;

	/**
	 * Download name
	 *
	 * @var string
	 */
	protected $downloadName;

	/**
	 * Identifier
	 *
	 * @var string
	 */
	protected $identifier;

	/**
	 * Creator
	 *
	 * @var string
	 */
	protected $creator;

	/**
	 * Source
	 *
	 * @var string
	 */
	protected $source;

	/**
	 * Alternative title
	 *
	 * @var string
	 */
	protected $alternative;

	/**
	 * Caption
	 *
	 * @var string
	 */
	protected $caption;

	/**
	 * Media type
	 *
	 * @var tx_mediaType
	 */
	protected $mediaType;
	
	/**
	 * Status
	 *
	 * @var string
	 */
	protected $status;
	
	/**
	 * Language
	 *
	 * @var string
	 */
	protected $language;
	
	/**
	 * Publisher
	 *
	 * @var string
	 */
	protected $publisher;
	
	/**
	 * LocationCountry
	 *
	 * @var string
	 */
	protected $locationCountry;
	
	/**
	 * LocationRegion
	 *
	 * @var string
	 */
	protected $locationRegion;
	
	/**
	 * LocationCity
	 *
	 * @var string
	 */
	protected $locationCity;
	
	/**
	 * Latitude
	 *
	 * @var float
	 */
	protected $latitude;
	
	/**
	 * Rank
	 *
	 * @var int
	 */
	protected $rank;

	/**
	 * Note
	 *
	 * @var string
	 */
	protected $note;
	
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
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * Returns the identifier
	 *
	 * @return string $identifier
	 */
	public function getIdentifier() {
		return $this->identifier;
	}

	/**
	 * Sets the identifier
	 *
	 * @param string $identifier
	 * @return void
	 */
	public function setIdentifier($identifier) {
		$this->identifier = $identifier;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the extension
	 *
	 * @return string $extension
	 */
	public function getExtension() {
		return $this->extension;
	}

	/**
	 * Sets the extension
	 *
	 * @param string $extension
	 * @return void
	 */
	public function setExtension($extension) {
		$this->extension = $extension;
	}

	/**
	 * Returns the creator
	 *
	 * @return string $creator
	 */
	public function getCreator() {
		return $this->creator;
	}

	/**
	 * Sets the creator
	 *
	 * @param string $creator
	 * @return void
	 */
	public function setCreator($creator) {
		$this->creator = $creator;
	}

	/**
	 * Returns the keywords
	 *
	 * @return string $keywords
	 */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
	 * Sets the keywords
	 *
	 * @param string $keywords
	 * @return void
	 */
	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	/**
	 * Initializes all Tx_Extbase_Persistence_ObjectStorage properties.
	 *
	 * @return void
	 */
	protected function initStorageObjects() {
		// empty
	}

	/**
	 * Returns the mimeType
	 *
	 * @return string $mimeType
	 */
	public function getMimeType() {
		return $this->mimeType;
	}

	/**
	 * Sets the mimeType
	 *
	 * @param string $mimeType
	 * @return void
	 */
	public function setMimeType($mimeType) {
		$this->mimeType = $mimeType;
	}

	/**
	 * Returns the creationDate
	 *
	 * @return DateTime $creationDate
	 */
	public function getCreationDate() {
		return $this->creationDate;
	}

	/**
	 * Sets the creationDate
	 *
	 * @param DateTime $creationDate
	 * @return void
	 */
	public function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
	}

	/**
	 * Returns the modificationDate
	 *
	 * @return DateTime $modificationDate
	 */
	public function getModificationDate() {
		return $this->modificationDate;
	}

	/**
	 * Sets the modificationDate
	 *
	 * @param DateTime $modificationDate
	 * @return void
	 */
	public function setModificationDate($modificationDate) {
		$this->modificationDate = $modificationDate;
	}

	/**
	 * Returns the creatorTool
	 *
	 * @return string $creatorTool
	 */
	public function getCreatorTool() {
		return $this->creatorTool;
	}

	/**
	 * Sets the creatorTool
	 *
	 * @param string $creatorTool
	 * @return void
	 */
	public function setCreatorTool($creatorTool) {
		$this->creatorTool = $creatorTool;
	}

	/**
	 * Returns the downloadName
	 *
	 * @return string $downloadName
	 */
	public function getDownloadName() {
		return $this->downloadName;
	}

	/**
	 * Sets the downloadName
	 *
	 * @param string $downloadName
	 * @return void
	 */
	public function setDownloadName($downloadName) {
		$this->downloadName = $downloadName;
	}

	/**
	 * Returns the source
	 *
	 * @return string $source
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * Sets the source
	 *
	 * @param string $source
	 * @return void
	 */
	public function setSource($source) {
		$this->source = $source;
	}

	/**
	 * Returns the alternative
	 *
	 * @return string $alternative
	 */
	public function getAlternative() {
		return $this->alternative;
	}

	/**
	 * Sets the alternative
	 *
	 * @param string $alternative
	 * @return void
	 */
	public function setAlternative($alternative) {
		$this->alternative = $alternative;
	}

	/**
	 * Returns the caption
	 *
	 * @return string $caption
	 */
	public function getCaption() {
		return $this->caption;
	}

	/**
	 * Sets the caption
	 *
	 * @param string $caption
	 * @return void
	 */
	public function setCaption($caption) {
		$this->caption = $caption;
	}
	
	/**
	 * Returns the status
	 *
	 * @return string $status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Sets the status
	 *
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->status = $status;
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
	
	/**
	 * Returns the publisher
	 *
	 * @return string $publisher
	 */
	public function getPublisher() {
		return $this->publisher;
	}

	/**
	 * Sets the publisher
	 *
	 * @param string $publisher
	 * @return void
	 */
	public function setPublisher($publisher) {
		$this->publisher = $publisher;
	}
	
	/**
	 * Returns the locationCountry
	 *
	 * @return string $locationCountry
	 */
	public function getLocationCountry() {
		return $this->locationCountry;
	}

	/**
	 * Sets the locationCountry
	 *
	 * @param string $locationCountry
	 * @return void
	 */
	public function setLocationCountry($locationCountry) {
		$this->locationCountry = $locationCountry;
	}
	
	/**
	 * Returns the locationRegion
	 *
	 * @return string $locationRegion
	 */
	public function getLocationRegion() {
		return $this->locationRegion;
	}

	/**
	 * Sets the locationRegion
	 *
	 * @param string $locationRegion
	 * @return void
	 */
	public function setLocationRegion($locationRegion) {
		$this->locationRegion = $locationRegion;
	}
	
	/**
	 * Returns the locationCity
	 *
	 * @return string $locationCity
	 */
	public function getLocationCity() {
		return $this->locationCity;
	}

	/**
	 * Sets the locationCity
	 *
	 * @param string $locationCity
	 * @return void
	 */
	public function setLocationCity($locationCity) {
		$this->locationCity = $locationCity;
	}
	
	/**
	 * Returns the latitude
	 *
	 * @return string $latitude
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * Sets the latitude
	 *
	 * @param string $latitude
	 * @return void
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}
	
	/**
	 * Returns the rank
	 *
	 * @return string $rank
	 */
	public function getRank() {
		return $this->rank;
	}

	/**
	 * Sets the rank
	 *
	 * @param string $rank
	 * @return void
	 */
	public function setRank($rank) {
		$this->rank = $rank;
	}
	
	/**
	 * Returns the note
	 *
	 * @return string $note
	 */
	public function getNote() {
		return $this->note;
	}

	/**
	 * Sets the note
	 *
	 * @param string $note
	 * @return void
	 */
	public function setNote($note) {
		$this->note = $note;
	}
}
?>