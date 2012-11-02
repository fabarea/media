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
 * A class to handle a SQL query
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Query  {

	/**
	 * @var \TYPO3\CMS\Media\QueryElement\Filter
	 */
	protected $filter;

	/**
	 * @var \TYPO3\CMS\Media\QueryElement\Order
	 */
	protected $order;

	/**
	 * @var int
	 */
	protected $offset = 0;

	/**
	 * @var int
	 */
	protected $limit = 0;

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandle;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter
	 */
	public function setFilter($filter) {
		$this->filter = $filter;
	}

	/**
	 * @return \TYPO3\CMS\Media\QueryElement\Filter
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order
	 */
	public function setOrder(\TYPO3\CMS\Media\QueryElement\Order $order) {
		$this->order = $order;
	}

	/**
	 * @return \TYPO3\CMS\Media\QueryElement\Order
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset) {
		$this->offset = (integer) $offset;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit) {
		$this->limit = (integer) $limit;
	}

	/**
	 * Render the SQL order by
	 *
	 * @return string
	 */
	public function renderOrder() {
		$orderings = $this->order->getOrderings();
		$orderBy = $delimiter = '';
		foreach ($orderings as $order => $direction) {
			$orderBy .= sprintf('%s %s %s', $delimiter, $order , $direction);
			$delimiter = ',';
		}
		return trim($orderBy);
	}

	/**
	 * Build the query and return its result
	 *
	 * @return string the query
	 */
	public function get() {
		$clause = 'deleted = 0';

		$groupBy = '';
		$orderBy = $this->renderOrder();
		$limit = $this->offset . ',' . $this->limit;

		return $this->databaseHandle->SELECTquery('*', 'sys_file', $clause, $groupBy, $orderBy, $limit);
	}
}

?>