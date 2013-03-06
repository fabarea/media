<?php
namespace TYPO3\CMS\Media\Domain\Model;

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
 * File Variant representation.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Variant {

	/**
	 * @var int
	 */
	protected $uid;

	/**
	 * @var int
	 */
	protected $pid;

	/**
	 * @var string
	 */
	protected $role;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File
	 */
	protected $original;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File
	 */
	protected $variant;

	/**
	 * Description of the file variation
	 *
	 * @var string
	 */
	protected $variation;

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor for a Media object.
	 *
	 * @param array $variantData
	 * @return \TYPO3\CMS\Media\Domain\Model\Variant
	 */
	public function __construct(array $variantData = array()) {
		$this->uid = empty($variantData['uid']) ? 0 : $variantData['uid'];
		$this->pid = empty($variantData['pid']) ? 0 : $variantData['pid'];
		$this->role = empty($variantData['role']) ? '' : $variantData['role'];
		$this->original = empty($variantData['original']) ? 0 : $variantData['original'];
		$this->variant = empty($variantData['variant']) ? 0 : $variantData['variant'];
		$this->variation = empty($variantData['variation']) ? 0 : $variantData['variation'];

		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * @return int
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * @param int $uid
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * @return int
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * @param int $pid
	 */
	public function setPid($pid) {
		$this->pid = $pid;
	}

	/**
	 * @return string
	 */
	public function getRole() {
		return $this->role;
	}

	/**
	 * @param string $role
	 */
	public function setRole($role) {
		$this->role = $role;
	}

	/**
	 * @return \TYPO3\CMS\Core\Resource\File
	 */
	public function getOriginal() {
		if ($this->original > 0) {
			/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
			$fileRepository = $this->objectManager->get('TYPO3\CMS\Core\Resource\FileRepository');
			$this->original = $fileRepository->findByUid($this->original);
		}
		return $this->original;
	}

	/**
	 * @param \TYPO3\CMS\Core\Resource\File $original
	 */
	public function setOriginal($original) {
		$this->original = $original;
	}

	/**
	 * @return \TYPO3\CMS\Core\Resource\File
	 */
	public function getVariant() {
		if ($this->variant > 0) {
			/** @var $fileRepository \TYPO3\CMS\Core\Resource\FileRepository */
			$fileRepository = $this->objectManager->get('TYPO3\CMS\Core\Resource\FileRepository');
			$this->variant = $fileRepository->findByUid($this->variant);
		}
		return $this->variant;
	}

	/**
	 * @param \TYPO3\CMS\Core\Resource\File $variant
	 */
	public function setVariant($variant) {
		$this->variant = $variant;
	}

	/**
	 * @return string
	 */
	public function getVariation() {
		return $this->variation;
	}

	/**
	 * @param string $variation
	 */
	public function setVariation($variation) {
		$this->variation = $variation;
	}

	/**
	 * Transform the object to an array
	 *
	 * @return array
	 */
	public function toArray() {
		$result = array(
			'pid' => $this->getPid(),
			'original' => is_object($this->original) ? $this->original->getUid() : $this->original,
			'variant' => is_object($this->variant) ? $this->variant->getUid() : $this->variant,
			'variation' => $this->getVariation(),
		);

		if ($this->getUid() > 0) {
			$result['uid'] = $this->getUid();
		}
		return $result;
	}

}
?>