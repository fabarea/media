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
 * Repository for accessing Asset
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class AssetRepository extends \TYPO3\CMS\Core\Resource\FileRepository {

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
	 * @var string
	 */
	protected $objectType = 'TYPO3\CMS\Media\Domain\Model\Asset';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
		$this->objectFactory = \TYPO3\CMS\Media\ObjectFactory::getInstance();
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * Update an asset with new information
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingUidException
	 * @param array $asset file information
	 * @return void
	 */
	public function updateAsset($asset = array()) {

		if (empty($asset['uid'])) {
			throw new \TYPO3\CMS\Media\Exception\MissingUidException('Missing Uid', 1351605542);
		}

		$data['sys_file'][$asset['uid']] = $asset;

		/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		//$tce->stripslashes_values = 0; @todo useful setting?
		$tce->start($data, array());
		$tce->process_datamap();
	}

	/**
	 * Add a new Asset into the repository.
	 *
	 * @param array $asset file information
	 * @return int
	 */
	public function addAsset($asset = array()) {

		if (empty($asset['pid'])) {
			$asset['pid'] = '0';
		}
		$key = 'NEW' . rand(100000, 999999);
		$data['sys_file'][$key] = $asset;

		/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		#$tce->stripslashes_values = 0; #@todo useful setting?
		$tce->start($data, array());
		$tce->process_datamap();

		return empty($tce->substNEWwithIDs[$key]) ? 0 : $tce->substNEWwithIDs[$key];
	}

	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\CMS\Media\Domain\Model\Asset[]
	 */
	public function findAll() {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');
		return $query->setRawResult($this->rawResult)
			->setObjectType($this->objectType)
			->execute();
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @param int $uid The identifier of the object to find
	 * @return \TYPO3\CMS\Media\Domain\Model\Asset The matching object
	 */
	public function findByUid($uid) {

		/** @var $filter \TYPO3\CMS\Media\QueryElement\Filter */
		$filter = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Filter');
		$filter->addConstraint('uid', $uid);

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Query');
		$result = $query->setRawResult($this->rawResult)
			->setObjectType($this->objectType)
			->setFilter($filter)
			->execute();

		if (is_array($result)) {
			$result = reset($result);
		}
		return $result;
	}

	/**
	 * Finds all Assets given a specified filter.
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter The filter the references must apply to
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order The order
	 * @param int $offset
	 * @param int $itemsPerPage
	 * @return \TYPO3\CMS\Media\Domain\Model\Asset[]
	 */
	public function findFiltered(\TYPO3\CMS\Media\QueryElement\Filter $filter, \TYPO3\CMS\Media\QueryElement\Order $order = NULL, $offset = NULL, $itemsPerPage = NULL) {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');

		$query->setFilter($filter);

		if ($order) {
			$query->setOrder($order);
		}

		if ($offset) {
			$query->setOffset($offset);
		}

		if ($itemsPerPage) {
			$query->setLimit($itemsPerPage);
		}

		return $query
			->setRawResult($this->rawResult)
			->setObjectType($this->objectType)
			->execute();
	}

	/**
	 * Count all Assets given a specified filter.
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter The filter the references must apply to
	 * @return int
	 */
	public function countFiltered(\TYPO3\CMS\Media\QueryElement\Filter $filter) {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');
		return $query->setFilter($filter)->count();
	}

	/**
	 * Removes an object from this repository.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset The object to remove
	 * @return boolean
	 */
	public function remove($asset) {
		$asset->getStorage()->deleteFile($asset);
		return $this->databaseHandle->exec_UPDATEquery('sys_file', 'uid = ' . $asset->getUid(), array('deleted' => 1));
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
		if (substr($methodName, 0, 6) === 'findBy' && strlen($methodName) > 7) {
			$propertyName = strtolower(substr(substr($methodName, 6), 0, 1)) . substr(substr($methodName, 6), 1);
			$result = $this->processMagicCall($propertyName, $arguments[0]);
		} elseif (substr($methodName, 0, 9) === 'findOneBy' && strlen($methodName) > 10) {
			$propertyName = strtolower(substr(substr($methodName, 9), 0, 1)) . substr(substr($methodName, 9), 1);
			$result = $this->processMagicCall($propertyName, $arguments[0], 'one');
		} elseif (substr($methodName, 0, 7) === 'countBy' && strlen($methodName) > 8) {
			$propertyName = strtolower(substr(substr($methodName, 7), 0, 1)) . substr(substr($methodName, 7), 1);
			$result = $this->processMagicCall($propertyName, $arguments[0], 'count');
		} else {
			throw new \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException('The method "' . $methodName . '" is not supported by the repository.', 1360838010);
		}
		return $result;
	}

	/**
	 * Handle the magic call by properly creating a Query object and returning its result.
	 *
	 * @param string $field
	 * @param string $value
	 * @param string $flag
	 * @return array
	 */
	 protected function processMagicCall($field, $value, $flag = '') {

		 /** @var $filter \TYPO3\CMS\Media\QueryElement\Filter */
		 $filter = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Filter');
		 $filter->addConstraint($field, $value);

		 // Add check if the object type returned is different than Media.
		 // @todo can be converted automatically with a Helper method
		 if ($this->objectType == 'TYPO3\CMS\Media\Domain\Model\Text') {
		    $filter->addConstraint('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT);
		 } elseif ($this->objectType == 'TYPO3\CMS\Media\Domain\Model\Image') {
			 $filter->addConstraint('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE);
		 } elseif ($this->objectType == 'TYPO3\CMS\Media\Domain\Model\Audio') {
			 $filter->addConstraint('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO);
		 } elseif ($this->objectType == 'TYPO3\CMS\Media\Domain\Model\Video') {
			 $filter->addConstraint('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO);
		 } elseif ($this->objectType == 'TYPO3\CMS\Media\Domain\Model\Application') {
			 $filter->addConstraint('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_SOFTWARE);
		 }

		 /** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		 $query = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Query');
		 $query->setRawResult($this->rawResult)
			 ->setObjectType($this->objectType)
			 ->setFilter($filter);

		 if ($flag == 'count') {
			 $result = $query->count();
		 } else {
			 $result = $query->execute();
		 }

		 return $flag == 'one' && !empty($result) ? reset($result) : $result;
	}

	/**
	 * @return boolean
	 */
	public function getRawResult() {
		return $this->rawResult;
	}

	/**
	 * @param boolean $rawResult
	 * @return \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 */
	public function setRawResult($rawResult) {
		$this->rawResult = $rawResult;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getObjectType() {
		return $this->objectType;
	}

	/**
	 * @param string $objectType
	 */
	public function setObjectType($objectType) {
		$this->objectType = $objectType;
	}

}

?>