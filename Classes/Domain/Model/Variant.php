<?php
namespace TYPO3\CMS\Media\Domain\Model;

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
use TYPO3\CMS\Core\Resource\ResourceStorage;

/**
 * Variant representation.
 */
class Variant extends Asset {

	/**
	 * Role of the Variant. This is also defined in the TCA of sys_file_variants:
	 *
	 * 0 -> none
	 * 1 -> thumbnail
	 * 2 -> subtitle
	 * 3 -> caption
	 * 4 -> alternative
	 *
	 * @var int
	 */
	protected $role = 1;

	/**
	 * Description of the file variation
	 *
	 * @var string
	 */
	protected $variation;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File
	 */
	protected $originalResource;

	/**
	 * Constructor for a Variant object.
	 *
	 * @param array $assetData
	 * @param ResourceStorage $storage
	 * @param File $originalResource
	 * @param array $variantData
	 * @return \TYPO3\CMS\Media\Domain\Model\Variant
	 */
	public function __construct(array $assetData = array(), ResourceStorage $storage, File $originalResource = NULL, array $variantData = array()) {
		parent::__construct($assetData, $storage);
		$this->originalResource = $originalResource;
		$this->role = empty($variantData['role']) ? 1 : $variantData['role'];
		$this->variation = empty($variantData['variation']) ? 0 : $variantData['variation'];
	}

	/**
	 * @return int
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * @param int $role
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	 * @return string
	 */
	public function getVariation() {
		return $this->getProperty('variation');
	}

	/**
	 * @param string $variation
	 */
	public function setVariation($variation) {
		$this->setProperty('variation', $variation);
	}

	/**
	 * @throws \Exception
	 * @return File
	 */
	public function getOriginalResource() {
		if (is_null($this->originalResource)) {
			$clause = sprintf('uid IN (SELECT original_resource FROM sys_file_variants WHERE variant_resource = %s)', $this->getUid());
			$fileData = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('uid', 'sys_file', $clause);
			if (empty($fileData)) {
				$message = sprintf('I can not retrieve original resource from variant resource "%s"', $this->getUid());
				throw new \Exception($message, 1390890031);
			}
			$this->originalResource = ResourceFactory::getInstance()->getFileObject($fileData['uid']);
		}
		return $this->originalResource;
	}

	/**
	 * @param \TYPO3\CMS\Core\Resource\File $original
	 */
	public function setOriginalResource($original) {
		$this->originalResource = $original;
	}

	/**
	 * Get the properties related to Variant only
	 *
	 * @return array
	 */
	public function getVariantProperties() {
		return array(
			'original_resource' => $this->getOriginalResource()->getUid(),
			'variant_resource' => $this->getUid(),
			'role' => $this->getRole(),
		);
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