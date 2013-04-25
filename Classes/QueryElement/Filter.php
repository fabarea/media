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
	 * Categories to be used to filter
	 *
	 * @var array
	 */
	protected $categories = array();

	/**
	 * fields to filter with their values
	 *
	 * @var array
	 */
	protected $constraints = array();

	/**
	 * Constructs a new Filter
	 *
	 * @param array $filters
	 */
	public function __construct($filters = array()) {
		$this->searchTerm = empty($filters['searchTerm']) ? '' : $filters['searchTerm'];

		if (!empty($filters['constraints']) && is_array($filters['constraints'])) {
			$this->constraints = $filters['constraints'];
		}
		if (!empty($filters['categories']) && is_array($filters['categories'])) {
			$this->categories = $filters['categories'];
		}
	}

	/**
	 * Returns categories to be used to filter.
	 *
	 * @return array
	 */
	public function getCategories() {
		return $this->categories;
	}

	/**
	 * Sets categories to be used to filter.
	 *
	 * @param array $categories The filter categories
	 * @return void
	 */
	public function setCategories(array $categories) {
		$this->categories = $categories;
	}

	/**
	 * Add a category to be used to filter. It could be either an integer or a string. Try using integer in priority which is more performant.
	 * Though, a string is also possible. It will firstly be converted to a possible uid.
	 *
	 * @param int|string|object $category
	 */
	public function addCategory($category) {
		$this->categories[] = $category;
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
	public function getConstraints() {
		return $this->constraints;
	}

	/**
	 * @param array $constraints
	 * @return \TYPO3\CMS\Media\QueryElement\Filter
	 */
	public function setConstraints($constraints) {
		$this->constraints = $constraints;
		return $this;
	}

	/**
	 * Add a value to be used for filtering a given field.
	 *
	 * @param string $field
	 * @param string $value
	 */
	public function addConstraint($field, $value) {
		$this->constraints[$field] = $value;
	}

}

?>