<?php
namespace TYPO3\CMS\Media\Tca;

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
 * A class to handle TCA grid configuration
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class GridService implements \TYPO3\CMS\Media\Tca\ServiceInterface {

	/**
	 * @var array
	 */
	protected $tca;

	/**
	 * @var string
	 */
	protected $tableName;

	/**
	 * __construct
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @param string $tableName
	 * @return \TYPO3\CMS\Media\Tca\GridService
	 */
	public function __construct($tableName) {

		$this->tableName = $tableName;

		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA($this->tableName);
		if (empty($GLOBALS['TCA'][$this->tableName])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('No TCA existence for table name: ' . $this->tableName, 1356945108);
		}
		$this->tca = $GLOBALS['TCA'][$this->tableName]['grid'];
	}

	/**
	 * Returns an array containing column names.
	 *
	 * @return array
	 */
	public function getFieldList() {
		return array_keys($this->tca['columns']);
	}

	/**
	 * Get the translation of a label given a column name.
	 *
	 * @param string $fieldName the name of the column
	 * @return string
	 */
	public function getLabel($fieldName) {
		$result = '';
		if ($this->hasLabel($fieldName)) {
			$field = $this->getField($fieldName);
			$result = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($field['label'], '');
		} elseif (\TYPO3\CMS\Media\Tca\ServiceFactory::getFieldService($this->tableName)->hasLabel($fieldName)) {
			$result = \TYPO3\CMS\Media\Tca\ServiceFactory::getFieldService($this->tableName)->getLabel($fieldName);
		}
		return $result;
	}

	/**
	 * Tell whether the column is internal or not.
	 *
	 * @param string $fieldName the name of the column
	 * @return boolean
	 */
	public function isSystem($fieldName) {
		return strpos($fieldName, '__') === 0;
	}

	/**
	 * Returns the field name given its position.
	 *
	 * @param string $position the position of the field in the grid
	 * @return int
	 */
	public function getFieldNameByPosition($position) {
		$fields = array_keys($this->getFields());
		if (empty($fields[$position])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('No field exist for position: ' . $position, 1356945119);
		}

		return $fields[$position];
	}

	/**
	 * Tell whether the column is not internal.
	 *
	 * @param string $fieldName the name of the column
	 * @return boolean
	 */
	public function isNotSystem($fieldName) {
		return !$this->isSystem($fieldName);
	}

	/**
	 * Returns an array containing the configuration of an column.
	 *
	 * @param string $fieldName the name of the column
	 * @return array
	 */
	public function getField($fieldName) {
		return $this->tca['columns'][$fieldName];
	}

	/**
	 * Returns an array containing column names.
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->tca['columns'];
	}

	/**
	 * Returns whether the column is sortable or not.
	 *
	 * @param string $fieldName the name of the column
	 * @return bool
	 */
	public function isSortable($fieldName) {
		$field = $this->getField($fieldName);
		return isset($field['sortable']) ? $field['sortable'] : TRUE;
	}

	/**
	 * Returns whether the column has a renderer.
	 *
	 * @param string $fieldName the name of the column
	 * @return bool
	 */
	public function hasRenderer($fieldName) {
		$field = $this->getField($fieldName);
		return empty($field['renderer']) ? FALSE : TRUE;
	}

	/**
	 * Returns a renderer.
	 *
	 * @param string $fieldName the name of the column
	 * @return string
	 */
	public function getRenderer($fieldName) {
		$field = $this->getField($fieldName);
		return empty($field['renderer']) ? '' : $field['renderer'];
	}

	/**
	 * Returns whether the column is visible or not.
	 *
	 * @param string $fieldName the name of the column
	 * @return bool
	 */
	public function isVisible($fieldName) {
		$field = $this->getField($fieldName);
		return isset($field['visible']) ? $field['visible'] : TRUE;
	}

	/**
	 * Returns whether the column has a label.
	 *
	 * @param string $fieldName the name of the column
	 * @return bool
	 */
	public function hasLabel($fieldName) {
		$field = $this->getField($fieldName);
		return empty($field['label']) ? FALSE : TRUE;
	}
}
?>