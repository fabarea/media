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
 * A class to handle TCA field configuration
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class FieldService implements \TYPO3\CMS\Media\Tca\ServiceInterface {

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
	 * @return \TYPO3\CMS\Media\Tca\FieldService
	 */
	public function __construct($tableName) {
		$this->tableName = $tableName;
		if (empty($GLOBALS['TCA'][$this->tableName])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('No TCA existence for table name: ' . $this->tableName, 1356945107);
		}
		$this->tca = $GLOBALS['TCA'][$this->tableName];
	}

	/**
	 * Returns an array containing column names
	 *
	 * @return array
	 */
	public function getFields() {
		return $this->tca['columns'];
	}

	/**
	 * Returns the configuration for a $field
	 *
	 * @param string $fieldName
	 * @return array
	 */
	public function getConfiguration($fieldName) {
		$fields = $this->getFields();
		return $fields[$fieldName]['config'];
	}

	/**
	 * Returns the configuration for a $field
	 *
	 * @param string $fieldName
	 * @return string
	 */
	public function getFieldType($fieldName) {
		if (is_int(strpos($fieldName, '--palette--'))) {
			return 'palette';
		}
		if (is_int(strpos($fieldName, '--widget--'))) {
			return 'widget';
		}
		$configuration = $this->getConfiguration($fieldName);
		$result = $configuration['type'];

		if (!empty($configuration['eval'])) {
			$parts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $configuration['eval']);
			if (in_array('datetime', $parts)) {
				$result = 'date';
			}
		}
		return $result;
	}

	/**
	 * Get the translation of a label given a column
	 *
	 * @param string $fieldName the name of the field
	 * @return string
	 */
	public function getLabel($fieldName) {
		$result = '';
		if ($this->hasLabel($fieldName)) {
			$field = $this->getField($fieldName);
			$result = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($field['label'], '');
		}
		return $result;
	}

	/**
	 * Get the translation of a label given a column
	 *
	 * @param string $fieldName the name of the field
	 * @param string $itemValue the item value to search for.
	 * @return string
	 */
	public function getLabelForItem($fieldName, $itemValue) {
		$result = '';
		$configuration = $this->getConfiguration($fieldName);
		if (! empty($configuration['items']) && is_array($configuration['items'])) {
			foreach ($configuration['items'] as $item) {
				if ($item[1] == $itemValue) {
					$result = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($item[0], '');
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * Get a possible icon given a field name an an item
	 *
	 * @param string $fieldName the name of the field
	 * @param string $itemValue the item value to search for.
	 * @return string
	 */
	public function getIconForItem($fieldName, $itemValue) {
		$result = '';
		$configuration = $this->getConfiguration($fieldName);
		if (!empty($configuration['items']) && is_array($configuration['items'])) {
			foreach ($configuration['items'] as $item) {
				if ($item[1] == $itemValue) {
					$result = empty($item[2]) ? '' : $item[2];
					break;
				}
			}
		}
		return $result;
	}

	/**
	 * Returns whether the field has a label
	 *
	 * @param string $fieldName the name of the field
	 * @return bool
	 */
	public function hasLabel($fieldName) {
		$field = $this->getField($fieldName);
		return empty($field['label']) ? FALSE : TRUE;
	}

	/**
	 * Returns whether the field is required
	 *
	 * @param string $fieldName the name of the field
	 * @return bool
	 */
	public function isRequired($fieldName) {
		$configuration = $this->getConfiguration($fieldName);
		$parts = array();
		if (! empty($configuration['eval'])) {
			$parts = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $configuration['eval']);
		}
		return in_array('required', $parts);
	}

	/**
	 * Returns an array containing the configuration of an column
	 *
	 * @param string $fieldName the name of the field
	 * @return array
	 */
	public function getField($fieldName) {
		return $this->tca['columns'][$fieldName];
	}

	/**
	 * Returns whether the field has relation (one to many, many to many)
	 *
	 * @param string $fieldName the name of the field
	 * @return bool
	 */
	public function hasRelation($fieldName) {
		$configuration = $this->getConfiguration($fieldName);
		return isset($configuration['foreign_table']);
	}

	/**
	 * Returns whether the field has no relation (one to many, many to many)
	 *
	 * @param string $fieldName the name of the field
	 * @return bool
	 */
	public function hasNoRelation($fieldName) {
		return !$this->hasRelation($fieldName);
	}

	/**
	 * Returns whether the field has one to many relation.
	 *
	 * @param string $fieldName the name of the field
	 * @return bool
	 */
	public function hasRelationOneToMany($fieldName) {
		$configuration = $this->getConfiguration($fieldName);
		return $this->hasRelation($fieldName) && !isset($configuration['MM']);
	}

	/**
	 * Returns whether the field has many to many relation.
	 *
	 * @param string $fieldName the name of the field
	 * @return bool
	 */
	public function hasRelationManyToMany($fieldName) {
		$configuration = $this->getConfiguration($fieldName);
		return $this->hasRelation($fieldName) && isset($configuration['MM']);
	}
}
?>