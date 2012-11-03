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
 * Repository for accessing media
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaRepository extends \TYPO3\CMS\Core\Resource\FileRepository {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandle;

	/**
	 * @var \TYPO3\CMS\Media\MediaFactory
	 */
	protected $mediaFactory;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
		$this->mediaFactory = \TYPO3\CMS\Media\MediaFactory::getInstance();
	}

	/**
	 * Update a Media Management media with new information
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingUidException
	 * @param array $media file information
	 * @return void
	 */
	public function updateMedia($media = array()) {

		if (empty($media['uid'])) {
			throw new \TYPO3\CMS\Media\Exception\MissingUidException('Missing Uid', 1351605542);
		}

		$data['sys_file'][$media['uid']] = $media;

		/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		//$tce->stripslashes_values = 0; @todo useful setting?
		$tce->start($data, array());
		$tce->process_datamap();
	}

	/**
	 * Add a new media in the repository
	 *
	 * @param array $media file information
	 * @return int
	 */
	public function addMedia($media = array()) {

		if (empty($media['pid'])) {
			$media['pid'] = '0';
		}
		$key = 'NEW' . rand(100000, 999999);
		$data['sys_file'][$key] = $media;

		/** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
		$tce = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\DataHandling\DataHandler');
		#$tce->stripslashes_values = 0; #@todo useful setting?
		$tce->start($data, array());
		$tce->process_datamap();

		return empty($tce->substNEWwithIDs[$key]) ? 0 : $tce->substNEWwithIDs[$key];
	}

	/**
	 * Finds all references by the specified filter
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter The filter the references must apply to
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order The order
	 * @param int $offset
	 * @param int $itemsPerPage
	 * @return \TYPO3\CMS\Media\Domain\Model\Media[]
	 */
	public function findAllByFilter(\TYPO3\CMS\Media\QueryElement\Filter $filter, \TYPO3\CMS\Media\QueryElement\Order $order = NULL, $offset = NULL, $itemsPerPage = NULL) {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');

		$query->setFilter($filter);
		$query->setOrder($order);

		if ($offset) {
			$query->setOffset($offset);
		}

		if ($itemsPerPage) {
			$query->setLimit($itemsPerPage);
		}

		$resource = $this->databaseHandle->sql_query($query->get());

		$items = array();
		while ($row = $this->databaseHandle->sql_fetch_assoc($resource)) {
			$items[] = $this->mediaFactory->createObject($row);
		}

		return $items;
	}

	/**
	 * Count all references by the specified filter
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Filter $filter The filter the references must apply to
	 * @return int
	 */
	public function countAllByFilter(\TYPO3\CMS\Media\QueryElement\Filter $filter) {

		/** @var $query \TYPO3\CMS\Media\QueryElement\Query */
		$query = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\QueryElement\Query');
		$query->setFilter($filter);

		return $this->databaseHandle->exec_SELECTcountRows('uid', 'sys_file', $query->renderClause());
	}

	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\CMS\Media\Domain\Model\Media[]
	 */
	public function findAll() {
		$itemList = array();
		$whereClause = 'deleted = 0';
		if ($this->type != '') {
			$whereClause .= ' AND ' . $this->typeField . ' = ' . $this->databaseHandle->fullQuoteStr($this->type, $this->table);
		}
		/** @var $res DB pointer */
		$res = $this->databaseHandle->exec_SELECTquery('*', $this->table, $whereClause);
		while ($row = $this->databaseHandle->sql_fetch_assoc($res)) {
			try {
				$itemList[] = $this->mediaFactory->createObject($row);
			} catch(\Exception $exception) {
				\TYPO3\CMS\Core\Utility\GeneralUtility::sysLog(
					$exception->getMessage(),
					'media',
					\TYPO3\CMS\Core\Utility\GeneralUtility::SYSLOG_SEVERITY_WARNING
				);
			}
		}
		$this->databaseHandle->sql_free_result($res);
		return $itemList;
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @throws \RuntimeException
	 * @throws \InvalidArgumentException
	 * @param int $uid The identifier of the object to find
	 * @return \TYPO3\CMS\Media\Domain\Model\Media The matching object
	 */
	public function findByUid($uid) {
		if (!\TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($uid)) {
			throw new \InvalidArgumentException('uid has to be integer.', 1350652667);
		}
		$row = $this->databaseHandle->exec_SELECTgetSingleRow('*', $this->table, 'uid=' . intval($uid) . ' AND deleted=0');
		if (count($row) === 0) {
			throw new \RuntimeException('Could not find row with uid "' . $uid . '" in table $this->table.', 1350652700);
		}
		return $this->mediaFactory->createObject($row);
	}

	/**
	 * Removes an object from this repository.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Media $media The object to remove
	 * @return boolean
	 */
	public function remove($media) {
		$media->getStorage()->deleteFile($media);
		return $this->databaseHandle->exec_UPDATEquery('sys_file', 'uid = ' . $media->getUid(), array('deleted' => 1));
	}
}

?>