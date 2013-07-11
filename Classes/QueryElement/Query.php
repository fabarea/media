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
	 * Constants representing a logical OR
	 */
	const LOGICAL_OR = 'OR';

	/**
	 * Constants representing a logical OR
	 */
	const LOGICAL_AND = 'AND';

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
	 * @var \TYPO3\CMS\Media\QueryElement\Match
	 */
	protected $match;

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
	 * @var \TYPO3\CMS\Media\Tca\FieldService
	 */
	protected $tcaFieldService;
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
		$this->objectFactory = \TYPO3\CMS\Media\ObjectFactory::getInstance();
		$this->tcaFieldService = \TYPO3\CMS\Media\Utility\TcaField::getService();
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
			if (empty($user->user['usergroup'])) {
				$user->user['usergroup'] = 0;
			}
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
		} elseif (TYPO3_MODE === 'FE' && $this->ignoreEnableFields != TRUE) {
			$clause .= $GLOBALS['TSFE']->sys_page->enableFields($this->tableName);
		}

		if (! is_null($this->match)) {
			$clauseSearchTerm = $this->getClauseSearchTerm();
			$clauseCategories = $this->getClauseManyToMany();

			if (strlen($clauseSearchTerm) > 0 && strlen($clauseCategories) > 0) {
				$queryPart = ' AND (%s) AND (%s)';
				if ($this->match->getDefaultLogicalOperator() === self::LOGICAL_OR) {
					$queryPart = ' AND (%s OR %s)';
				}
				$clause .= sprintf($queryPart, $clauseSearchTerm, $clauseCategories);
			} elseif (strlen($clauseSearchTerm) > 0) {
				$clause .= sprintf(' AND (%s)', $clauseSearchTerm);
			} elseif (strlen($clauseCategories) > 0) {
				$clause .= sprintf(' AND (%s)', $clauseCategories);
			}

			$clause .= $this->getClauseMain();
		}

		return $clause;
	}

	/**
	 * Get the category clause
	 *
	 * @return string
	 */
	protected function getClauseManyToMany() {
		$clause = '';

		foreach ($this->match->getMatches() as $field => $values) {

			if ($this->tcaFieldService->hasRelationManyToMany($field)) {

				$tcaConfiguration = $this->tcaFieldService->getConfiguration($field);

				// First check if any it is of type string and try to retrieve a corresponding uid
				$_items = array();
				foreach ($values as $item) {
					if (is_object($item) && method_exists($item, 'getUid')) {
						$item = $item->getUid();
					}

					// TRUE means this is a character chain given.
					// So, try to be smart by checking if the string correspond to a uid in $tca_configuration['foreign_table'].
					if (!is_numeric($item)) {
						$escapedValue = $this->databaseHandle->escapeStrForLike($item, $tcaConfiguration['foreign_table']);
						$_clause = sprintf('%s LIKE "%%%s%%"',
							\TYPO3\CMS\Media\Tca\ServiceFactory::getTableService($tcaConfiguration['foreign_table'])->getLabel(),
							$escapedValue
						);

						$records = $this->databaseHandle->exec_SELECTgetRows('uid', $tcaConfiguration['foreign_table'], $_clause);
						if (!empty($records)) {
							foreach ($records as $record) {
								$_items[] = $record['uid'];
							}
						}
					} else {
						$_items[] = $item;
					}
				}

				if (! empty($_items)) {

					$template = <<<EOF
	uid IN (
		SELECT
			uid_foreign
		FROM
			%s
		WHERE
			tablenames = "{$this->tableName}" AND uid_local IN (%s))
EOF;
					// Add MM search
					$clause .= sprintf($template, $tcaConfiguration['MM'], implode(',', $_items));
				}
			}
		}
		return $clause;
	}

	/**
	 * Get the search term clause
	 *
	 * @return string
	 */
	protected function getClauseMain() {
		$clause = '';
		// Add constraints to the request
		// @todo Implement OR. For now only support AND. Take inspiration from logicalAnd and logicalOr.
		// @todo Add matching method $query->matching($query->equals($propertyName, $value))
		foreach ($this->match->getMatches() as $field => $value) {
			if ($this->tcaFieldService->hasNoRelation($field)) {
				$clause .= sprintf(' AND %s = %s',
					$field,
					$this->databaseHandle->fullQuoteStr($value, $this->tableName)
				);
			}
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

		if ($this->match->getSearchTerm()) {
			$searchTerm = $this->databaseHandle->escapeStrForLike($this->match->getSearchTerm(), $this->tableName);
			$searchParts = array();

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

				$overlay = \TYPO3\CMS\Media\Utility\Overlays::getOverlayRecords($this->tableName, array($row['uid']), $GLOBALS['TSFE']->sys_language_uid);
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
	 * @return \TYPO3\CMS\Media\QueryElement\Match
	 */
	public function getMatch() {
		return $this->match;
	}

	/**
	 * @param \TYPO3\CMS\Media\QueryElement\Match $match
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 */
	public function setMatch(\TYPO3\CMS\Media\QueryElement\Match $match) {
		$this->match = $match;
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
