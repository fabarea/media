<?php
namespace TYPO3\CMS\Media\Domain\Repository;

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
 * Repository for accessing File Variant.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class VariantRepository implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileRepository
	 */
	protected $fileRepository;

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandle;

	/**
	 * @var \TYPO3\CMS\Media\ObjectFactory
	 */
	protected $objectFactory;

	/**
	 * @var string
	 */
	protected $tableName = 'sys_file_variants';

	/**
	 * Tell whether it is a raw result (array) or object being returned.
	 *
	 * @var bool
	 */
	protected $rawResult = FALSE;

	/**
	 * @var string
	 */
	protected $objectType = 'TYPO3\CMS\Media\Domain\Model\Variant';

	/**
	 * @return \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 */
	public function __construct() {
		$this->fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\FileRepository');
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
		$this->objectFactory = \TYPO3\CMS\Media\ObjectFactory::getInstance();
	}

	/**
	 * Return an array of Variant objects
	 *
	 * @return bool|array|\TYPO3\CMS\Media\Domain\Model\Variant[]
	 */
	public function findAll() {
		$records = $this->databaseHandle->exec_SELECTgetRows('*', $this->tableName, '');
		if (is_array($records) && !$this->rawResult) {
			$objects = array();
			foreach ($records as $record) {
				$objects[] = $this->objectFactory->createObject($record, $this->objectType);
			}
			$records = $objects;
		}
		return $records;
	}

	/**
	 * Return the number of Variants
	 *
	 * @return \TYPO3\CMS\Media\Domain\Model\Variant[]
	 */
	public function countAll() {
		return $this->databaseHandle->exec_SELECTcountRows('*', $this->tableName);
	}

	/**
	 * Return one Variant given its uid
	 *
	 * @param int $uid
	 * @return bool|array|\TYPO3\CMS\Media\Domain\Model\Variant
	 */
	public function findByUid($uid) {
		$record = $this->databaseHandle->exec_SELECTgetSingleRow('*', $this->tableName, 'uid = ' . $uid);

		if (is_array($record) && !$this->rawResult) {
			$record = $this->objectFactory->createObject($record, $this->objectType);
		}
		return $record;
	}

	/**
	 * @param \TYPO3\CMS\Media\Domain\Model\Variant $variant
	 * @return int
	 */
	public function update(\TYPO3\CMS\Media\Domain\Model\Variant $variant) {
		$this->databaseHandle->exec_UPDATEquery($this->tableName, 'uid = ' . $variant->getUid(), $variant->toArray());
	}

	/**
	 * @param \TYPO3\CMS\Media\Domain\Model\Variant $variant
	 * @return int
	 */
	public function add(\TYPO3\CMS\Media\Domain\Model\Variant $variant) {
		$this->databaseHandle->exec_INSERTquery($this->tableName, $variant->toArray());
		return $this->databaseHandle->sql_insert_id();
	}

	/**
	 * @param \TYPO3\CMS\Media\Domain\Model\Variant $variant
	 * @return void
	 */
	public function remove(\TYPO3\CMS\Media\Domain\Model\Variant $variant) {
		$this->removeByUid($variant->getUid());
	}

	/**
	 * @param int $uid
	 * @return void
	 */
	public function removeByUid($uid) {
		$this->databaseHandle->exec_DELETEquery($this->tableName, 'uid =' . $uid);
	}

	/**
	 * Dispatches magic methods (findBy[Property]())
	 *
	 * @param string $methodName The name of the magic method
	 * @param string $arguments The arguments of the magic method
	 * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException
	 * @return mixed
	 * @api
	 */
	public function __call($methodName, $arguments) {
		$result = NULL;
		if (substr($methodName, 0, 6) === 'findBy' && strlen($methodName) > 7) {
			$propertyName = strtolower(substr(substr($methodName, 6), 0, 1)) . substr(substr($methodName, 6), 1);
			$result = $this->processMagicFindBy($propertyName, $arguments[0]);
		} elseif (substr($methodName, 0, 9) === 'findOneBy' && strlen($methodName) > 10) {
			$propertyName = strtolower(substr(substr($methodName, 9), 0, 1)) . substr(substr($methodName, 9), 1);
			$result = $this->processMagicFindOneBy($propertyName, $arguments[0]);
		} elseif (substr($methodName, 0, 7) === 'countBy' && strlen($methodName) > 8) {
			$propertyName = strtolower(substr(substr($methodName, 7), 0, 1)) . substr(substr($methodName, 7), 1);
			$result = $this->processMagicCountBy($propertyName, $arguments[0]);
		} else {
			throw new \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException('The method "' . $methodName . '" is not supported by the repository.', 1360838010);
		}
		return $result;
	}

	/**
	 * Handle the magic call findBy*
	 *
	 * @param string $field
	 * @param string $value
	 * @return null|array|\TYPO3\CMS\Media\Domain\Model\Variant[]
	 */
	protected function processMagicFindBy($field, $value) {
		$clause = sprintf('%s = %s',
			$field,
			$this->databaseHandle->fullQuoteStr($value, $this->tableName)
		);
		$records = $this->databaseHandle->exec_SELECTgetRows('*', $this->tableName, $clause);
		if (is_array($records) && !$this->rawResult) {
			$objects = array();
			foreach ($records as $record) {
				$objects[] = $this->objectFactory->createObject($record, $this->objectType);
			}
			$records = $objects;
		}
		return $records;
	}

	/**
	 * Handle the magic call findOneBy*
	 *
	 * @param string $field
	 * @param string $value
	 * @return null|\TYPO3\CMS\Media\Domain\Model\Variant
	 */
	protected function processMagicFindOneBy($field, $value) {
		$clause = sprintf('%s = %s',
			$field,
			$this->databaseHandle->fullQuoteStr($value, $this->tableName)
		);
		$record = $this->databaseHandle->exec_SELECTgetSingleRow('*', $this->tableName, $clause);
		if (is_array($record) && !$this->rawResult) {
			$record = $this->objectFactory->createObject($record, $this->objectType);
		}
		return $record;
	}

	/**
	 * Handle the magic call countBy*
	 *
	 * @param string $field
	 * @param string $value
	 * @return int
	 */
	protected function processMagicCountBy($field, $value) {
		$clause = sprintf('%s = %s',
			$field,
			$this->databaseHandle->fullQuoteStr($value, $this->tableName)
		);
		return $this->databaseHandle->exec_SELECTcountRows('*', $this->tableName, $clause);
	}

	/**
	 * @return boolean
	 */
	public function getRawResult() {
		return $this->rawResult;
	}

	/**
	 * @param boolean $rawResult
	 * @return \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 */
	public function setRawResult($rawResult) {
		$this->rawResult = $rawResult;
		return $this;
	}
}

?>