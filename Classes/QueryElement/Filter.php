<?php
namespace TYPO3\CMS\Media\QueryElement;

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
 * Filter class for conditions that will apply to a query
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Filter  {

	/**
	 * The filter search term.
	 *
	 * @var string
	 */
	protected $searchTerm = '';

	/**
	 * A category to filter by
	 *
	 * @var integer
	 */
	protected $category = 0;

	/**
	 * fields to filter with their values
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Constructs a new Filter
	 *
	 * @param array $filters
	 */
	public function __construct($filters = array()) {
		$this->searchTerm = empty($filters['searchTerm']) ? '' : $filters['searchTerm'];
	}

	/**
	 * Sets this filter category
	 *
	 * @param int $category The filter category
	 * @return void
	 */
	public function setCategory($category) {
		$this->category = $category;
	}

	/**
	 * Returns the filter category
	 *
	 * @return int The filter category
	 */
	public function getCategory() {
		return $this->category;
	}

	/**
	 * Sets this filter searchTerm
	 *
	 * @param string $searchTerm The filter searchTerm
	 * @return void
	 */
	public function setSearchTerm($searchTerm) {
		$this->searchTerm = $searchTerm;
	}

	/**
	 * Returns the filter searchTerm
	 *
	 * @return string The filter searchTerm
	 */
	public function getSearchTerm() {
		return $this->searchTerm;
	}

	/**
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	/**
	 *
	 * @param string $field
	 * @param string $value
	 */
	public function setFields($field, $value) {
		$this->fields[$field] = $value;
	}

}

?>