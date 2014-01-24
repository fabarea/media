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
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * Repository for accessing Asset
 */
class AssetRepository extends FileRepository {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandle;

	/**
	 * @var ObjectFactory
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
	 * @var array
	 */
	protected $objectTypes = array(
		File::FILETYPE_TEXT => 'TYPO3\CMS\Media\Domain\Model\Text',
		File::FILETYPE_IMAGE => 'TYPO3\CMS\Media\Domain\Model\Image',
		File::FILETYPE_AUDIO => 'TYPO3\CMS\Media\Domain\Model\Audio',
		File::FILETYPE_VIDEO => 'TYPO3\CMS\Media\Domain\Model\Video',
		File::FILETYPE_APPLICATION => 'TYPO3\CMS\Media\Domain\Model\Application',
	);

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
		$this->objectFactory = ObjectFactory::getInstance();
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\CMS\Media\Domain\Model\Asset[]
	 */
	public function findAll() {

		$query = $this->createQuery();
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

		$matcher = $this->createMatch()->addMatch('uid', $uid);

		$query = $this->createQuery();
		$result = $query->setRawResult($this->rawResult)
			->setObjectType($this->objectType)
			->setMatcher($matcher)
			->setFilterVariant(FALSE)
			->execute();

		if (is_array($result)) {
			$result = reset($result);
		}
		return $result;
	}

	/**
	 * Finds all Assets given specified matches.
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Matcher $matcher
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order The order
	 * @param int $limit
	 * @param int $offset
	 * @return \TYPO3\CMS\Media\Domain\Model\Asset[]
	 */
	public function findBy(\TYPO3\CMS\Media\QueryElement\Matcher $matcher, \TYPO3\CMS\Media\QueryElement\Order $order = NULL, $limit = NULL, $offset = NULL) {

		$query = $this->createQuery()->setMatcher($matcher);

		if ($order) {
			$query->setOrder($order);
		}

		if ($offset) {
			$query->setOffset($offset);
		}

		if ($limit) {
			$query->setLimit($limit);
		}

		return $query
			->setRawResult($this->rawResult)
			->setObjectType($this->objectType)
			->execute();
	}

	/**
	 * Count all Assets given specified matches.
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Matcher $matcher
	 * @return int
	 */
	public function countBy(\TYPO3\CMS\Media\QueryElement\Matcher $matcher) {
		$query = $this->createQuery();
		return $query->setMatcher($matcher)->count();
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
	 * Returns a query for objects of this repository
	 *
	 * @return \TYPO3\CMS\Media\QueryElement\Query
	 * @api
	 */
	public function createQuery() {
		return $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Query');
	}

	/**
	 * Returns a matcher object for this repository
	 *
	 * @return \TYPO3\CMS\Media\QueryElement\Matcher
	 * @return object
	 */
	public function createMatch() {
		return $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Matcher');
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
	 * @return \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 */
	public function setObjectType($objectType) {
		$this->objectType = $objectType;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getObjectTypes() {
		return $this->objectTypes;
	}

	/**
	 * @param array $objectTypes
	 */
	public function setObjectTypes($objectTypes) {
		$this->objectTypes = $objectTypes;
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

		$matcher = $this->createMatch()->addMatch($field, $value);

		// Add "automatic" file type restriction if method get called from child repository.
		$fileType = $this->getFileType($this->objectType);
		if ($fileType > 0) {
			$matcher->addMatch('type', $fileType);
		}

		$query = $this->createQuery();
		$query->setRawResult($this->rawResult)
			->setObjectType($this->objectType)
			->setMatcher($matcher);

		if ($flag == 'count') {
			$result = $query->count();
		} else {
			$result = $query->execute();
		}

		return $flag == 'one' && !empty($result) ? reset($result) : $result;
	}

	/**
	 * Return the file type according to the object name
	 *
	 * @param string $objectType
	 * @return int
	 */
	protected function getFileType($objectType) {
		$key = array_search($objectType, $this->objectTypes);
		return $key === FALSE ? 0 : $key;
	}

	/**
	 * Update an asset with new information
	 * This method is tight to the BE for now
	 * @todo write a patch to persist File relations in FAL
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return void
	 */
	public function update($asset) {

		$this->getFileIndexRepository()->update($asset);
		$assetData = $asset->toArray();
		$values = array();

		// Required by the Data Handler.
		if (is_array($assetData['categories'])) {
			$values['categories'] = implode(',', $assetData['categories']);

			$metadataProperties = $asset->_getMetaData();
			$data['sys_file_metadata'][$metadataProperties['uid']] = $values;

			/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
			$tce = $this->objectManager->get('TYPO3\CMS\Core\DataHandling\DataHandler');
			$tce->start($data, array());
			$tce->process_datamap();
		}
	}
}

?>