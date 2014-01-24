<?php
namespace TYPO3\CMS\Media\Domain\Repository;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Media\Domain\Model\Variant;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * Repository for accessing File Variant.
 */
class VariantRepository extends AssetRepository {

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
	 * @var \TYPO3\CMS\Media\Index\VariantIndexRepository
	 * @inject
	 */
	protected $variantIndexRepository;

	/**
	 * Return an array of Variants.
	 *
	 * @return Variant[]
	 */
	public function findAll() {
		$clause = 'is_variant = 1';
		$clause .= $this->getWhereClauseForEnabledFields();
		$records = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', $this->table, $clause);

		$variants = array();

		if (!empty($records)) {
			foreach ($records as $record) {
				$file = ResourceFactory::getInstance()->getFileObject($record['uid']);
				$variants[] = ObjectFactory::getInstance()->createObject($file->getProperties(), $this->objectType);
			}
		}
		return $variants;
	}

	/**
	 * Return the number of Variants
	 *
	 * @return Variant[]
	 */
	public function countAll() {
		return $this->getDatabaseConnection()->exec_SELECTcountRows('*', $this->table);
	}

	/**
	 * Return one Variant given its uid
	 *
	 * @param int $uid
	 * @return Variant
	 */
	public function findByUid($uid) {
		$file = ResourceFactory::getInstance()->getFileObject($uid);
		return ObjectFactory::getInstance()->createObject($file->getProperties(), $this->objectType);
	}

	/**
	 * @param File $file
	 * @return Variant[]
	 */
	public function findByOriginalResource(File $file) {

		$clause = 'is_variant = 1';
		$clause .= $this->getWhereClauseForEnabledFields();
		$clause .= sprintf(' AND uid IN (SELECT variant_resource FROM sys_file_variants WHERE original_resource = %s)', $file->getUid());
		$records = $this->getDatabaseConnection()->exec_SELECTgetRows('uid', $this->table, $clause);

		$variants = array();
		if (!empty($records)) {
			foreach ($records as $record) {
				$file = ResourceFactory::getInstance()->getFileObject($record['uid']);
				$variants[] = ObjectFactory::getInstance()->createObject($file->getProperties(), $this->objectType);
			}
		}
		return $variants;
	}

	/**
	 * @param Variant $variant
	 * @throws \Exception
	 * @return boolean
	 */
	public function update($variant) {

		// Persist property of sys_file
		$this->variantIndexRepository->update($variant);

		$clause = sprintf('original_resource = %s AND variant_resource = %s',
			$variant->getOriginalResource()->getUid(),
			$variant->getUid()
		);

		$result = $this->getDatabaseConnection()->exec_UPDATEquery('sys_file_variants', $clause, $variant->getVariantProperties());
		if (!$result) {
			throw new \Exception('I could not update a sys_file_variants relation', 1390909941);
		}
		return $result;
	}

	/**
	 * @param Variant $variant
	 * @throws \Exception
	 * @return int
	 */
	public function add($variant) {

		// Persist property of sys_file
		$this->variantIndexRepository->add($variant);

		$result = $this->getDatabaseConnection()->exec_INSERTquery('sys_file_variants', $variant->getVariantProperties());
		if (!$result) {
			throw new \Exception('I could not create a sys_file_variants relation', 1390909940);
		}
		return $this->getDatabaseConnection()->sql_insert_id();
	}

	/**
	 * Returns a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}

?>