<?php
namespace TYPO3\CMS\Media\Utility;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Lorenz Ulrich <lorenz.ulrich@visol.ch>
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
 * A class for Media folder utilities
 */
class MediaFolder {

	/**
	 * Get all available media folders
	 *
	 * @return array
	 */
	static public function findFolders() {
		/** @var $databaseHandle \TYPO3\CMS\Core\Database\DatabaseConnection */
		$databaseHandle = $GLOBALS['TYPO3_DB'];
		$rows = array();
		$whereClause = 'module=' . $databaseHandle->fullQuoteStr('media', 'pages') . ' AND deleted=0';
		if ($mediaFolders = $databaseHandle->exec_SELECTgetRows('uid,pid,title,doktype', 'pages', $whereClause, '', '', '', 'uid')) {
			$rows = $mediaFolders;
		}
		return $rows;
	}

	/**
	 * Fetch list of media folders from extension configuration, if not set, get current folders or create one
	 *
	 * @return string
	 */
	static public function getPidList() {

		$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['media']);
		if (!empty($extensionConfiguration['media_folders'])) {
			/** @var $databaseHandle \TYPO3\CMS\Core\Database\DatabaseConnection */
			$databaseHandle = $GLOBALS['TYPO3_DB'];
			$mediaFolderPidList = $databaseHandle->cleanIntList($extensionConfiguration['media_folders']);
		} else {
			$mediaFolders = self::findFolders();
			if (empty($mediaFolders)) {
				self::createFolder();
				$mediaFolders = self::findFolders();
			}
			$mediaFolderPidList = implode(',', array_keys($mediaFolders));
		}
		return $mediaFolderPidList;
	}

	/**
	 * Create a Media folder
	 *
	 * @param integer $pid The PID of the folder which is 0 by default to place the folder in the root.
	 * @return mixed
	 */
	static public function createFolder($pid=0) {
		/** @var $databaseHandle \TYPO3\CMS\Core\Database\DatabaseConnection */
		$databaseHandle = $GLOBALS['TYPO3_DB'];

		$data = array(
			'pid' => $pid,
			'sorting' => 99999,
			'perms_user' => 31,
			'perms_group' => 31,
			'perms_everybody' => 31,
			'title' => 'Media categories',
			'doktype' => 254, // folder
			'module' => 'media',
			'crdate' => time(),
			'tstamp' => time(),
		);

		return $databaseHandle->exec_INSERTquery('pages', $data);

	}
}
?>