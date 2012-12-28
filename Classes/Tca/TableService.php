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
 * A class to handle TCA ctrl.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class TableService implements \TYPO3\CMS\Media\Tca\ServiceInterface {

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
	 * @return \TYPO3\CMS\Media\Tca\TableService
	 */
	public function __construct($tableName) {
		$this->tableName = $tableName;
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA($this->tableName);
		if (empty($GLOBALS['TCA'][$this->tableName])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('No TCA existence for table name: ' . $this->tableName, 1356945106);
		}
		$this->tca = $GLOBALS['TCA'][$this->tableName]['ctrl'];
	}

	/**
	 * Get the label name of table name.
	 *
	 * @return string
	 */
	public function getLabel() {
		$result = '';
		if (! empty($this->tca['label'])) {
			$result = $this->tca['label'];
		}
		return $result;
	}

	/**
	 * Returns the searchable fields
	 *
	 * @return string
	 */
	public function getSearchableFields() {
		return $this->tca['searchFields'];
	}
}
?>