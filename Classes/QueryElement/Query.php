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
class Query {

	/**
	 * @var string
	 */
	protected $tableName = 'sys_file';

	/**
	 * The default object type being returned for the Media Object Factory
	 *
	 * @var string
	 */
	protected $objectType = 'TYPO3\CMS\Media\Domain\Model\Asset';

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
	 * @var \TYPO3\CMS\Media\ObjectFactory
	 */
	protected $objectFactory;

	/**
	 * Tell whether it is a raw result (array) or object being returned.
	 *
	 * @var bool
	 */
	protected $rawResult = FALSE;

	/**
	 * A flag indicating whether all or some enable fields should be ignored. If TRUE, all enable fields are ignored.
	 * If--in addition to this--enableFieldsToBeIgnored is set, only fields specified there are ignored. If FALSE, all
	 * enable fields are taken into account, regardless of the enableFieldsToBeIgnored setting.
	 *
	 * @var boolean
	 */
	protected $ignoreEnableFields = FALSE;

	/**
	 * Tell whether the storage will be respected. It normally should, but exception may happen.
	 *
	 * @var bool
	 */
	protected $respectStorage = TRUE;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
		$this->objectFactory = \TYPO3\CMS\Media\ObjectFactory::getInstance();
	}

	/**
	 * Render the SQL "orderBy" part.
	 *
	 * @return string
	 */
	public function renderOrder() {
		$orderBy = '';
		if (!is_null($this->order)) {
			$orderings = $this->order->getOrderings();
			$orderBy = $delimiter = '';
			foreach ($orderings as $order => $direction) {
				$orderBy .= sprintf('%s %s %s', $delimiter, $order , $direction);
				$delimiter = ',';
			}
		}
		return trim($orderBy);
	}

	/**
	 * Render the SQL "limit" part.
	 *
	 * @return string
	 */
	public function renderLimit() {
		$limit = '';
		if ($this->limit > 0) {
			$limit = $this->offset . ',' . $this->limit;
		}
		return $limit;
	}

	/**
	 * Render the SQL "where" part
	 *
	 * @return string
	 */
	public function renderClause() {

		$clause = 'deleted = 0 AND is_variant = 0 AND sys_language_uid = 0';

		/** @var $user \TYPO3\CMS\Core\Authentication\BackendUserAuthentication */
		$user = $GLOBALS['BE_USER'];
		$settingManagement = \TYPO3\CMS\Media\Utility\Setting::getInstance();

		// Add segment to handle BE Permission
		if (TYPO3_MODE == 'BE' && $settingManagement->get('permission') && !$user->isAdmin()) {
			$clause .= sprintf(' AND uid IN (SELECT uid_local FROM sys_file_begroups_mm WHERE uid_foreign IN(%s))', $user->user['usergroup']);
		}

		if ($this->respectStorage) {
			$clause = sprintf('%s AND storage = %s',
				$clause,
				$settingManagement->get('storage')
			);
		}

		if (TYPO3_MODE === 'BE' && $this->ignoreEnableFields) {
			$clause .= \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields($this->tableName);
		} elseif (TYPO3_MODE === 'FE' && $this->ignoreEnableFields) {
			$clause .= $GLOBALS['TSFE']->sys_page->enableFields($this->tableName);
		}

		if (! is_null($this->filter)) {
			$clauseSearchTerm = $this->getClauseSearchTerm();
			$clauseCategories = $this->getClauseCategories();

			if (strlen($clauseSearchTerm) > 0 && strlen($clauseCategories) > 0) {
				$clause .= sprintf(' AND (%s OR %s)', $clauseSearchTerm, $clauseCategories);
			} elseif (strlen($clauseSearchTerm) > 0) {
				$clause .= sprintf(' AND (%s)', $clauseSearchTerm);
			} elseif (strlen($clauseCategories) > 0) {
				$clause .= sprintf(' AND (%s)', $clauseCategories);
			}

			$clause .= $this->getClauseConstraints();
		}

		return $clause;
	}

	/**
	 * Get the category clause
	 *
	 * @return string
	 */
	protected function getClauseCategories() {
		$clause = '';

		$categories = $this->filter->getCategories();
		if (! empty($categories)) {

			// First check if any category is of type string and try to retrieve a corresponding uid
			$_categories = array();
			foreach ($categories as $category) {
				if (is_object($category) && method_exists($category, 'getUid')) {
					$category = $category->getUid();
				}

				// TRUE means this is a character chain given. So, try to be smart by checking if the string correspond to a category id.
				if (!is_numeric($category)) {
					$escapedCategory = $this->databaseHandle->escapeStrForLike($category, $this->tableName);
					$records = $this->databaseHandle->exec_SELECTgetRows('uid', 'sys_category', sprintf('title LIKE "%%%s%%"', $escapedCategory));
					if (! empty($records)) {
						foreach ($records as $record) {
							$_categories[] = $record['uid'];
						}
					}
				} else {
					$_categories[] = $category;
				}
			}

			if (! empty($_categories)) {

				$template = <<<EOF
uid IN (
	SELECT
		uid_foreign
	FROM
		sys_category_record_mm
	WHERE
		tablenames = "sys_file" AND uid_local IN (%s))
EOF;
				// Add category search
				$clause = sprintf($template, implode(',', $_categories));
			}
		}
		return $clause;
	}

	/**
	 * Get the search term clause
	 *
	 * @return string
	 */
	protected function getClauseConstraints() {
		$clause = '';
		// Add constraints to the request
		// @todo Implement OR. For now only support AND. Take inspiration from logicalAnd and logicalOr.
		// @todo Add matching method $query->matching($query->equals($propertyName, $value))
		foreach ($this->filter->getConstraints() as $field => $value) {
			$clause .= sprintf(' AND %s = "%s"',
				$field,
				$this->databaseHandle->escapeStrForLike($value, $this->tableName)
			);
		}
		return $clause;
	}

	/**
	 * Get the search term clause
	 *
	 * @return string
	 */
	protected function getClauseSearchTerm() {
		$clause = '';

		if ($this->filter->getSearchTerm()) {
			$searchTerm = $this->databaseHandle->escapeStrForLike($this->filter->getSearchTerm(), $this->tableName);
			$searchParts = array();
			\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA($this->tableName);

			$fields = explode(',', \TYPO3\CMS\Media\Utility\TcaTable::getService()->getSearchableFields());

			foreach ($fields as $field) {
				$fieldType = \TYPO3\CMS\Media\Utility\TcaField::getService()->getFieldType($field);
				if ($fieldType == 'text' OR $fieldType == 'input') {
					$searchParts[] = sprintf('%s LIKE "%%%s%%"', $field, $searchTerm);
				}
			}
			$searchParts[] = sprintf('uid = "%s"', $searchTerm);
			$clause = implode(' OR ', $searchParts);
		}
		return $clause;
	}

	/**
	 * Build the query and return its result
	 *
	 * @return string the query
	 */
	public function getQuery() {
		$clause = $this->renderClause();
		$orderBy = $this->renderOrder();
		$limit = $this->renderLimit();

		return $this->databaseHandle->SELECTquery('*', $this->tableName, $clause, $groupBy = '', $orderBy, $limit);
	}

	/**
	 * Execute a query and return its result set.
	 *
	 * @return mixed
	 */
	public function execute() {
		$resource = $this->databaseHandle->sql_query($this->getQuery());
		$items = array();
		while ($row = $this->databaseHandle->sql_fetch_assoc($resource)) {

			// Get record overlay if needed
			if (TYPO3_MODE == 'FE' && $GLOBALS['TSFE']->sys_language_uid > 0) {

				$overlay = \TYPO3\CMS\Media\Utility\Overlays::getOverlayRecords('sys_file', array($row['uid']), $GLOBALS['TSFE']->sys_language_uid);
				if (!empty($overlay[$row['uid']])) {
					$key = key($overlay[$row['uid']]);
					$row = $overlay[$row['uid']][$key];
				}
			}

			if (!$this->rawResult) {
				try {
					$row = $this->objectFactory->createObject($row, $this->objectType);
				} catch (\Exception $exception) {
					$logger = \TYPO3\CMS\Media\Utility\Logger::getInstance($this);
					$logger->warning($exception->getMessage());
				}
			}
			$items[] = $row;
		}
		return $items;
	}

	/**
	 * Execute a query and count its items.
	 *
	 * @return int
	 */
	public function count() {
		$clause = $this->renderClause();
		return $this->databaseHandle->exec_SELECTcountRows('*', $this->tableName, $clause);
	}

	/**
	 * @return \TYPO3\CMS\Media\QueryElement\Filter
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setFilter(\TYPO3\CMS\Media\QueryElement\Filter $filter) {
		$this->filter = $filter;
		return $this;
	}

	/**
	 * @return \TYPO3\CMS\Media\QueryElement\Order
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setOrder(\TYPO3\CMS\Media\QueryElement\Order $order) {
		$this->order = $order;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @param int $offset
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setOffset($offset) {
		$this->offset = (integer) $offset;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @param int $limit
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setLimit($limit) {
		$this->limit = (integer) $limit;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getRawResult() {
		return $this->rawResult;
	}

	/**
	 * @param boolean $rawResult
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setRawResult($rawResult) {
		$this->rawResult = $rawResult;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getObjectType() {
		return $this->objectType;
	}

	/**
	 * @param boolean $objectType
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setObjectType($objectType) {
		$this->objectType = $objectType;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getIgnoreEnableFields() {
		return $this->ignoreEnableFields;
	}

	/**
	 * @param boolean $ignoreEnableFields
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setIgnoreEnableFields($ignoreEnableFields) {
		$this->ignoreEnableFields = $ignoreEnableFields;
		return $this;
	}
}

?>