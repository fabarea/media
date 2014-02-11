<?php
namespace TYPO3\CMS\Media\QueryElement;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Mario Matzulla <mario@matzullas.de>
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
 * A page browser
 */
class Pager  {

	/**
	 * Total amount of entries
	 *
	 * @var integer
	 */
	protected $count;

	/**
	 * Current offset
	 *
	 * @var integer
	 */
	protected $offset;

	/**
	 * Current page index
	 *
	 * @var integer
	 */
	protected $page;

	/**
	 * Number of items per page
	 *
	 * @var integer
	 */
	protected $limit = 10;

	/**
	 * Constructs a new Pager

	 */
	public function __construct() {
		$this->page = 1;
	}

	/**
	 * Returns the total amount of entries
	 *
	 * @return int
	 */
	public function getCount() {
		return $this->count;
	}

	/**
	 * Sets the total amount of entries
	 *
	 * @param int $count
	 */
	public function setCount($count) {
		$this->count = $count;
	}

	/**
	 * Returns the current page index
	 *
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * Sets the current page index
	 *
	 * @param int $page
	 */
	public function setPage($page) {
		$this->page = $page;
	}

	/**
	 * Returns the current limit index
	 *
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * Sets the current limit index
	 *
	 * @param int $limit
	 */
	public function setLimit($limit) {
		$this->limit = $limit;
	}

	/**
	 * @return Array Items to display
	 */
	public function getDisplayItems() {
		$last = $this->getLastPage();
		if ($last == 1) {
			return null;
		}
		$values = Array();
		for ($i = 1; $i <= $last; $i++) {
			$values[] = Array('key' => $i, 'value' => $i);
		}
		return $values;
	}

	/**
	 * @return int The last page index
	 */
	public function getLastPage() {
		$last = intval($this->count / $this->limit);
		if ($this->count % $this->limit > 0) {
			$last++;
		}
		return $last;
	}

	/**
	 * @return int The previous page index. Minimum value is 1
	 */
	public function getPreviousPage() {
		$prev = $this->page - 1;
		if ($prev < 1) {
			$prev = 1;
		}
		return $prev;
	}

	/**
	 * @return int The next page index. Maximum valus is the last page
	 */
	public function getNextPage() {
		$next = $this->page + 1;
		$last = $this->getLastPage();
		if ($next > $last) {
			$next = $last;
		}
		return $next;
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset) {
		$this->offset = $offset;
	}
}
