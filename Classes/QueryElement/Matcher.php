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
 * Matcher class for conditions that will apply to a query.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 * @deprecated use TYPO3\CMS\Vidi\Persistence\Matcher instead
 */
class Matcher {

	/**
	 * @var string
	 */
	protected $searchTerm = '';

	/**
	 * @var string
	 */
	protected $defaultLogicalOperator = 'AND';

	/**
	 * Contains associative values used for finding matches array($fieldName => $value)
	 *
	 * @var array
	 */
	protected $matches = array();

	/**
	 * @var \TYPO3\CMS\Media\Tca\FieldService
	 */
	protected $tcaFieldService;

	/**
	 * Constructs a new Matcher
	 *
	 * @param array $matches associative array($field => $value)
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 */
	public function __construct($matches = array()) {
		$this->tcaService = \TYPO3\CMS\Media\Utility\TcaField::getService();
		$this->matches = $matches;
	}

	/**
	 * Add a category to be used as match. It could be either an integer or a string. Try using integer in priority which is more efficient.
	 * A string is also possible to be given. The Query object will attempt to find / convert to a category uid.
	 *
	 * @param int|string|object $category
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 */
	public function addCategory($category) {
		$this->addMatch('categories', $category);
		return $this;
	}

	/**
	 * @param string $searchTerm
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 */
	public function setSearchTerm($searchTerm) {
		$this->searchTerm = $searchTerm;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSearchTerm() {
		return $this->searchTerm;
	}

	/**
	 * @return array
	 */
	public function getMatches() {
		return $this->matches;
	}

	/**
	 * @param array $matches
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 */
	public function setMatches($matches) {
		$this->matches = $matches;
		return $this;
	}

	/**
	 * Add a value to be used for filtering a given field.
	 *
	 * @param string $field
	 * @param string $value
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 */
	public function addMatch($field, $value) {
		if ($this->tcaService->hasRelationManyToMany($field)) {
			if (empty($this->matches[$field])) {
				$this->matches[$field] = array();
			}
			$this->matches[$field][] = $value;
		} else {
			$this->matches[$field] = $value;
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getDefaultLogicalOperator() {
		return $this->defaultLogicalOperator;
	}

	/**
	 * @param string $defaultLogicalOperator value must be "or", "and"
	 */
	public function setDefaultLogicalOperator($defaultLogicalOperator) {
		$this->defaultLogicalOperator = $defaultLogicalOperator;
	}
}

?>