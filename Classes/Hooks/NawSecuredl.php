<?php
namespace TYPO3\CMS\Media\Hooks;

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2013
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
 * ************************************************************* */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class providing a Hook for naw_securedl.
 */
class NawSecuredl {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Constructor
	 */
	public function __construct(){
		$this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
	}

	/**
	 * Check file permission against frontend user group.
	 *
	 * @param mixed $params array('pObj' => $pObj)
	 * @param \tx_nawsecuredl_output $secureDownload
	 * @return void
	 */
	public function preOutput($params, $secureDownload) {

		// Initialize Frontend
		$GLOBALS['TSFE'] = GeneralUtility::makeInstance('TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
		$GLOBALS['TSFE']->sys_page = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
		$GLOBALS['TSFE']->initFEuser();

		$file = GeneralUtility::_GP('file');

		/** @var \TYPO3\CMS\Core\Resource\StorageRepository $storageRepository */
		$storageRepository = $this->objectManager->get('TYPO3\CMS\Core\Resource\StorageRepository');

		foreach ($storageRepository->findAll() as $storage) {

			$rootFolderIdentifier = $storage->getRootLevelFolder()->getPublicUrl();

			// Remove the segment from mount point
			$identifier = str_replace($rootFolderIdentifier, '', $file);

			// Makes sure the identifier start with a slash
			$identifier = '/' . ltrim($identifier, '/');

			/** @var \TYPO3\CMS\Media\Domain\Model\Asset $file */
			$fileRecord = $this->getDatabaseConnexion()->exec_SELECTgetSingleRow('uid, identifier', 'sys_file', 'identifier = "' . $identifier . '"');
			if (!empty($fileRecord)) {

				$frontendUserGroups = $this->findFrontendUserGroups($fileRecord);

				if (!empty($frontendUserGroups)) {

					$affectedUserGroups = GeneralUtility::trimExplode(',', $this->getFrontendUser()->user['usergroup'], TRUE);

					$this->checkFilePermission($affectedUserGroups, $frontendUserGroups);
				}
				break;
			}
		}
	}

	/**
	 * Check file permission
	 *
	 * @param array $affectedUserGroups
	 * @param array $frontendUserGroups
	 * @return void
	 */
	public function checkFilePermission(array $affectedUserGroups, array $frontendUserGroups) {
		$result = FALSE;
		foreach ($affectedUserGroups as $affectedUserGroup) {
			if (in_array($affectedUserGroup, $frontendUserGroups)) {
				$result = TRUE;
				break;
			}
		}

		// No access
		if ($result === FALSE) {
			header('HTTP/1.0 403 Forbidden');
			die("Accessing the resource is forbidden!");
		}
	}

	/**
	 * Find all Frontend User Groups
	 *
	 * @param array $fileRecord
	 * @return array
	 */
	public function findFrontendUserGroups(array $fileRecord) {

		// Fetch if there is a relations
		$frontendUserGroups = $this->getDatabaseConnexion()->exec_SELECTgetRows('uid_foreign', 'sys_file_fegroups_mm', 'uid_local = ' . $fileRecord['uid']);
		$result = array();
		foreach ($frontendUserGroups as $frontendUserGroup) {
			$result[] = $frontendUserGroup['uid_foreign'];
		}

		return $result;
	}

	/**
	 * Return a mount point according to an identifier
	 *
	 * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
	 */
	protected function getFrontendUser() {
		return $GLOBALS['TSFE']->fe_user;
	}

	/**
	 * Return a mount point according to an identifier
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnexion() {
		return $GLOBALS['TYPO3_DB'];
	}
}