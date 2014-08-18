<?php
namespace TYPO3\CMS\Media\Domain\Model;

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
use TYPO3\CMS\Media\Service\ThumbnailService;

/**
 * Media representation in the file abstraction layer.
 */
class Asset extends File {

	/**
	 * Alternative title
	 *
	 * @var string
	 */
	protected $alternative;

	/**
	 * @var string
	 */
	protected $caption;

	/**
	 * @var string
	 */
	protected $colorSpace;

	/**
	 * Content creation date
	 *
	 * @var \DateTime
	 */
	protected $creationDate;

	/**
	 * Content modification date
	 *
	 * @var \DateTime
	 */
	protected $modificationDate;

	/**
	 * @var string
	 */
	protected $creator;

	/**
	 * @var string
	 */
	protected $creatorTool;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $downloadName;

	/**
	 * @var string
	 */
	protected $duration;

	/**
	 * @var int
	 */
	protected $height;

	/**
	 * @var string
	 */
	protected $keywords;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var float
	 */
	protected $latitude;

	/**
	 * @var string
	 */
	protected $locationCity;

	/**
	 * @var string
	 */
	protected $locationCountry;

	/**
	 * @var string
	 */
	protected $locationRegion;

	/**
	 * @var float
	 */
	protected $longitude;

	/**
	 * @var string
	 */
	protected $mimeType;

	/**
	 * @var string
	 */
	protected $extension;

	/**
	 * @var string
	 */
	protected $note;

	/**
	 * @var string
	 */
	protected $pages;

	/**
	 * @var string
	 */
	protected $publisher;

	/**
	 * @var int
	 */
	protected $ranking;

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string
	 * @validate NotEmpty
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $unit;

	/**
	 * @var int
	 */
	protected $width;

	/**
	 * Returns the alternative
	 *
	 * @return string $alternative
	 */
	public function getAlternative() {
		return $this->getProperty('alternative');
	}

	/**
	 * Sets the alternative
	 *
	 * @param string $alternative
	 * @return void
	 */
	public function setAlternative($alternative) {
		$this->properties['alternative'] = $alternative;
	}

	/**
	 * Returns the caption
	 *
	 * @return string $caption
	 */
	public function getCaption() {
		return $this->getProperty('caption');
	}

	/**
	 * Sets the caption
	 *
	 * @param string $caption
	 * @return void
	 */
	public function setCaption($caption) {
		$this->properties['caption'] = $caption;
	}

	/**
	 * Returns the color space
	 *
	 * @return string $colorSpace
	 */
	public function getColorSpace() {
		return $this->getProperty('color_space');
	}

	/**
	 * Sets the color space
	 *
	 * @param string $colorSpace
	 * @return void
	 */
	public function setColorSpace($colorSpace) {
		$this->properties['color_space'] = $colorSpace;
	}

	/**
	 * Returns the creationDate
	 *
	 * @return \DateTime $creationDate
	 */
	public function getCreationDate() {
		return $this->getProperty('creation_date');
	}

	/**
	 * Sets the creationDate
	 *
	 * @param \DateTime $creationDate
	 * @return void
	 */
	public function setCreationDate($creationDate) {
		$this->properties['creation_date'] = $creationDate;
	}

	/**
	 * Returns the modificationDate
	 *
	 * @return \DateTime $modificationDate
	 */
	public function getModificationDate() {
		return $this->getProperty('modification_date');
	}

	/**
	 * Sets the modificationDate
	 *
	 * @param \DateTime $modificationDate
	 * @return void
	 */
	public function setModificationDate($modificationDate) {
		$this->properties['modification_date'] = $modificationDate;
	}

	/**
	 * Returns the creator
	 *
	 * @return string $creator
	 */
	public function getCreator() {
		return $this->getProperty('creator');
	}

	/**
	 * Sets the creator
	 *
	 * @param string $creator
	 * @return void
	 */
	public function setCreator($creator) {
		$this->properties['creator'] = $creator;
	}

	/**
	 * Returns the creatorTool
	 *
	 * @return string $creatorTool
	 */
	public function getCreatorTool() {
		return $this->getProperty('creator_tool');
	}

	/**
	 * Sets the creatorTool
	 *
	 * @param string $creatorTool
	 * @return void
	 */
	public function setCreatorTool($creatorTool) {
		$this->properties['creator_tool'] = $creatorTool;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->getProperty('description');
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->properties['description'] = $description;
	}

	/**
	 * Returns the download name
	 *
	 * @return string $downloadName
	 */
	public function getDownloadName() {
		return $this->getProperty('download_name');
	}

	/**
	 * Sets the download name
	 *
	 * @param string $downloadName
	 * @return void
	 */
	public function setDownloadName($downloadName) {
		$this->properties['download_name'] = $downloadName;
	}

	/**
	 * Returns the duration
	 *
	 * @return string $duration
	 */
	public function getDuration() {
		return $this->getProperty('duration');
	}

	/**
	 * Sets the duration
	 *
	 * @param string $duration
	 * @return void
	 */
	public function setDuration($duration) {
		$this->properties['duration'] = $duration;
	}

	/**
	 * Returns the height
	 *
	 * @return string $height
	 */
	public function getHeight() {
		return $this->getProperty('height');
	}

	/**
	 * Sets the height
	 *
	 * @param string $height
	 * @return void
	 */
	public function setHeight($height) {
		$this->properties['height'] = $height;
	}

	/**
	 * Returns the keywords
	 *
	 * @return string $keywords
	 */
	public function getKeywords() {
		return $this->getProperty('keywords');
	}

	/**
	 * Sets the keywords
	 *
	 * @param string $keywords
	 * @return void
	 */
	public function setKeywords($keywords) {
		$this->properties['keywords'] = $keywords;
	}

	/**
	 * Returns the language
	 *
	 * @return string $language
	 */
	public function getLanguage() {
		return $this->getProperty('language');
	}

	/**
	 * Sets the language
	 *
	 * @param string $language
	 * @return void
	 */
	public function setLanguage($language) {
		$this->properties['language'] = $language;
	}

	/**
	 * Returns the latitude
	 *
	 * @return string $latitude
	 */
	public function getLatitude() {
		return $this->getProperty('latitude');
	}

	/**
	 * Sets the latitude
	 *
	 * @param string $latitude
	 * @return void
	 */
	public function setLatitude($latitude) {
		$this->properties['latitude'] = $latitude;
	}

	/**
	 * Returns the locationCity
	 *
	 * @return string $locationCity
	 */
	public function getLocationCity() {
		return $this->getProperty('location_city');
	}

	/**
	 * Sets the locationCity
	 *
	 * @param string $locationCity
	 * @return void
	 */
	public function setLocationCity($locationCity) {
		$this->properties['location_city'] = $locationCity;
	}

	/**
	 * Returns the locationCountry
	 *
	 * @return string $locationCountry
	 */
	public function getLocationCountry() {
		return $this->getProperty('location_country');
	}

	/**
	 * Sets the locationCountry
	 *
	 * @param string $locationCountry
	 * @return void
	 */
	public function setLocationCountry($locationCountry) {
		$this->properties['location_country'] = $locationCountry;
	}

	/**
	 * Returns the locationRegion
	 *
	 * @return string $locationRegion
	 */
	public function getLocationRegion() {
		return $this->getProperty('location_region');
	}

	/**
	 * Sets the locationRegion
	 *
	 * @param string $locationRegion
	 * @return void
	 */
	public function setLocationRegion($locationRegion) {
		$this->properties['location_region'] = $locationRegion;
	}

	/**
	 * Returns the longitude
	 *
	 * @return string $longitude
	 */
	public function getLongitude() {
		return $this->getProperty('longitude');
	}

	/**
	 * Sets the longitude
	 *
	 * @param string $longitude
	 * @return void
	 */
	public function setLongitude($longitude) {
		$this->properties['longitude'] = $longitude;
	}

	/**
	 * Returns the MIME type
	 *
	 * @return string $mimeType
	 */
	public function getMimeType() {
		return $this->getProperty('mime_type');
	}

	/**
	 * Sets the MIME type
	 *
	 * @param string $mimeType
	 * @return void
	 */
	public function setMimeType($mimeType) {
		$this->properties['mime_type'] = $mimeType;
	}

	/**
	 * @return string
	 */
	public function getExtension() {
		return $this->getProperty('extension');
	}

	/**
	 * @param string $extension
	 */
	public function setExtension($extension) {
		$this->properties['extension'] = $extension;
	}

	/**
	 * Returns the note
	 *
	 * @return string $note
	 */
	public function getNote() {
		return $this->getProperty('note');
	}

	/**
	 * Sets the note
	 *
	 * @param string $note
	 * @return void
	 */
	public function setNote($note) {
		$this->properties['note'] = $note;
	}

	/**
	 * Returns the pages
	 *
	 * @return string $pages
	 */
	public function getPages() {
		return $this->getProperty('pages');
	}

	/**
	 * Sets the pages
	 *
	 * @param string $pages
	 * @return void
	 */
	public function setPages($pages) {
		$this->properties['pages'] = $pages;
	}

	/**
	 * Returns the publisher
	 *
	 * @return string $publisher
	 */
	public function getPublisher() {
		return $this->getProperty('publisher');
	}

	/**
	 * Sets the publisher
	 *
	 * @param string $publisher
	 * @return void
	 */
	public function setPublisher($publisher) {
		$this->properties['publisher'] = $publisher;
	}

	/**
	 * Returns the ranking
	 *
	 * @return string $ranking
	 */
	public function getRanking() {
		return $this->getProperty('ranking');
	}

	/**
	 * Sets the ranking
	 *
	 * @param string $ranking
	 * @return void
	 */
	public function setRanking($ranking) {
		$this->properties['ranking'] = $ranking;
	}

	/**
	 * Returns the source
	 *
	 * @return string $source
	 */
	public function getSource() {
		return $this->getProperty('source');
	}

	/**
	 * Sets the source
	 *
	 * @param string $source
	 * @return void
	 */
	public function setSource($source) {
		$this->properties['source'] = $source;
	}

	/**
	 * Returns the status
	 *
	 * @return string $status
	 */
	public function getStatus() {
		return $this->getProperty('status');
	}

	/**
	 * Sets the status
	 *
	 * @param string $status
	 * @return void
	 */
	public function setStatus($status) {
		$this->properties['status'] = $status;
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->getProperty('title');
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->properties['title'] = $title;
	}

	/**
	 * Returns the type
	 *
	 * @return string $type
	 */
	public function getType() {
		return $this->getProperty('type') === '' ? 0 : (int) $this->getProperty('type');
	}

	/**
	 * Sets the type
	 *
	 * @param string $type
	 * @return void
	 */
	public function setType($type) {
		$this->properties['type'] = $type;
	}

	/**
	 * Returns the unit
	 *
	 * @return string $unit
	 */
	public function getUnit() {
		return $this->getProperty('unit');
	}

	/**
	 * Sets the unit
	 *
	 * @param string $unit
	 * @return void
	 */
	public function setUnit($unit) {
		$this->properties['unit'] = $unit;
	}

	/**
	 * Returns the width
	 *
	 * @return string $width
	 */
	public function getWidth() {
		return $this->getProperty('width');
	}

	/**
	 * Sets the width
	 *
	 * @param string $width
	 * @return void
	 */
	public function setWidth($width) {
		$this->properties['width'] = $width;
	}

	/**
	 * Sets a custom property name. Useful for unit testing.
	 *
	 * @param string $property
	 * @param string $value
	 * @return void
	 */
	public function setProperty($property, $value) {
		$this->updateProperties(array($property => $value));
	}

}
