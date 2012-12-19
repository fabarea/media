<?php
namespace TYPO3\CMS\Media\Utility;

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
class Grid implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $tca;

	/**
	 * Returns a class instance
	 *
	 * @return \TYPO3\CMS\Media\Utility\Grid
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\Grid');
	}

	/**
	 * __construct
	 *
	 * @throw \TYPO3\CMS\Media\Exception\MissingTcaConfigurationException
	 * @return \TYPO3\CMS\Media\Utility\Grid
	 */
	public function __construct() {

		// Index "columns" is required
		if (empty($GLOBALS['TCA']['sys_file']['grid'])) {
			throw new \TYPO3\CMS\Media\Exception\MissingTcaConfigurationException('Missing TCA key "sys_file/grid/columns"', 1351865723);
		}

		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_file');
		$this->tca = $GLOBALS['TCA']['sys_file']['grid'];
	}

	/**
	 * Returns an array containing column names
	 *
	 * @return array
	 */
	public function getListOfColumns() {
		return array_keys($this->tca['columns']);
	}

	/**
	 * Get the translation of the column
	 *
	 * @param string $column the name of the column
	 * @return string
	 */
	public function getLabel($column) {
		$result = '';
		if ($this->hasLabel($column)) {
			$configuration = $this->getColumn($column);
			$result = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($configuration['label'], '');
		} elseif (!empty($GLOBALS['TCA']['sys_file']['columns'][$column]['label'])) {
			$label = $GLOBALS['TCA']['sys_file']['columns'][$column]['label'];
			$result = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($label, 'media');
		}
		return $result;
	}

	/**
	 * Tell whether the column is internal or not
	 *
	 * @param string $column the name of the column
	 * @return boolean
	 */
	public function isInternal($column) {
		$configuration = $this->getColumn($column);
		return empty($configuration['internal_type']) ? FALSE : $configuration['internal_type'];
	}

	/**
	 * Tell whether the column is not internal
	 *
	 * @param string $column the name of the column
	 * @return boolean
	 */
	public function isNotInternal($column) {
		return !$this->isInternal($column);
	}

	/**
	 * Returns an array containing the configuration of an column
	 *
	 * @param string $column the name of the column
	 * @return array
	 */
	public function getColumn($column) {
		return $this->tca['columns'][$column];
	}

	/**
	 * Returns an array containing column names
	 *
	 * @return array
	 */
	public function getColumns() {
		return $this->tca['columns'];
	}

	/**
	 * Returns whether the column is sortable or not
	 *
	 * @param string $column the name of the column
	 * @return bool
	 */
	public function isSortable($column) {
		$configuration = $this->getColumn($column);
		return isset($configuration['sortable']) ? $configuration['sortable'] : TRUE;
	}

	/**
	 * Returns whether the column is sortable or not
	 * @todo comment
	 *
	 * @param string $column the name of the column
	 * @return bool
	 */
	public function hasRenderer($column) {
		$configuration = $this->getColumn($column);
		return empty($configuration['renderer']) ? FALSE : TRUE;
	}

	/**
	 * Returns whether the column is sortable or not
	 *
	 * @todo comment
	 * @param string $column the name of the column
	 * @return string
	 */
	public function getRenderer($column) {
		$configuration = $this->getColumn($column);
		return empty($configuration['renderer']) ? '' : $configuration['renderer'];
	}

	/**
	 * Returns whether the column is sortable or not
	 *
	 * @param string $column the name of the column
	 * @return bool
	 */
	public function isVisible($column) {
		$configuration = $this->getColumn($column);
		return isset($configuration['visible']) ? $configuration['visible'] : TRUE;
	}

	/**
	 * Returns whether the column has a label
	 *
	 * @param string $column the name of the column
	 * @return bool
	 */
	public function hasLabel($column) {
		$configuration = $this->getColumn($column);
		return empty($configuration['label']) ? FALSE : TRUE;
	}
}
?>