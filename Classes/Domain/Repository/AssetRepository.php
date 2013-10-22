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

/**
 * Repository for accessing Asset
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
	 * @var array
	 */
	protected $objectTypes = array(
		\TYPO3\CMS\Core\Resource\File::FILETYPE_TEXT => 'TYPO3\CMS\Media\Domain\Model\Text',
		\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => 'TYPO3\CMS\Media\Domain\Model\Image',
		\TYPO3\CMS\Core\Resource\File::FILETYPE_AUDIO => 'TYPO3\CMS\Media\Domain\Model\Audio',
		\TYPO3\CMS\Core\Resource\File::FILETYPE_VIDEO => 'TYPO3\CMS\Media\Domain\Model\Video',
		\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION => 'TYPO3\CMS\Media\Domain\Model\Application',
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
		$this->objectFactory = \TYPO3\CMS\Media\ObjectFactory::getInstance();
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * Update an asset with new information
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingUidException
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset|array $asset file information
	 * @return void
	 */
	public function updateAsset($asset) {
		if (is_object($asset)) {
			/** @var $assetObject \TYPO3\CMS\Media\Domain\Model\Asset */
			$assetObject = $asset;
			$asset = $assetObject->toArray();
		}

		if (empty($asset['uid'])) {
			throw new \TYPO3\CMS\Media\Exception\MissingUidException('Missing Uid', 1351605542);
		}

		if (is_array($asset['categories'])) {
			$asset['categories'] = implode(',', $asset['categories']);
		} else {
			unset($asset['categories']);
		}

		$data['sys_file'][$asset['uid']] = $asset;

		/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tce = $this->objectManager->get('TYPO3\CMS\Core\DataHandling\DataHandler');
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
		$tce = $this->objectManager->get('TYPO3\CMS\Core\DataHandling\DataHandler');
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
			->setRespectStorage(FALSE)
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
	 * Removes an object from this repository.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset The object to remove
	 * @return boolean
	 */
	public function remove($asset) {
		$result = FALSE;
		if (is_object($asset) && $asset->exists()) {

			// Delete record
			$result = $this->databaseHandle->exec_DELETEquery('sys_file', 'uid = ' . $asset->getUid());
			$asset->delete();

			// Get the recycler folder. Create on if needed.
			// @todo revamp recycler concept.
			#$recyclerFolderName = \TYPO3\CMS\Media\Utility\ConfigurationUtility::getInstance()->get('recycler_folder');
			#$storageObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorage();
			#$storageObject->deleteFile($asset);
		    #if (! $storageObject->hasFolder($recyclerFolderName)) {
			#	$storageObject->createFolder($recyclerFolderName);
			#}
			#$recyclerFolder = $storageObject->getFolder($recyclerFolderName);

			#// Move the asset to the recycler
			#$asset->moveTo($recyclerFolder, $asset->getName(), 'renameNewFile');
		}
		return $result;
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
}

?>