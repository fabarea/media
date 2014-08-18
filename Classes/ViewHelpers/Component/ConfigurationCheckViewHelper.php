<?php
namespace TYPO3\CMS\Media\ViewHelpers\Component;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Utility\StorageUtility;

/**
 * View helper which renders a button for uploading assets.
 */
class ConfigurationCheckViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var array
	 */
	public $notAllowedMountPoints = array();

	/**
	 * @var ResourceStorage
	 */
	public $storage;

	/**
	 * @var string
	 */
	protected $extensionName = 'media';

	/**
	 * @var \TYPO3\CMS\Vidi\Module\ModuleLoader
	 * @inject
	 */
	protected $moduleLoader;

	/**
	 * Renders a button for uploading assets.
	 *
	 * @return string
	 */
	public function render() {

		$result = '';

		// Check whether storage is configured or not.
		if ($this->checkStorageNotConfigured()) {
			$result .= $this->formatMessageStorageNotConfigured();
		}

		// Check whether storage is online or not.
		if ($this->checkStorageOffline()) {
			$result .= $this->formatMessageStorageOffline();
		}

		// Check all mount points of the storage are available
		if (!$this->checkMountPoints()) {
			$result .= $this->formatMessageMountPoints();
		}

		return $result;
	}

	/**
	 * Check whether the storage is correctly configured.
	 *
	 * @return boolean
	 */
	protected function checkStorageNotConfigured() {
		$currentStorage = StorageUtility::getInstance()->getCurrentStorage();
		$storageRecord = $currentStorage->getStorageRecord();

		// Take the storage fields and check whether some data was initialized.
		$fields = array(
			'maximum_dimension_original_image',
			'extension_allowed_file_type_1',
			'extension_allowed_file_type_2',
			'extension_allowed_file_type_3',
			'extension_allowed_file_type_4',
			'extension_allowed_file_type_5',
			'mount_point_file_type_1',
			'mount_point_file_type_2',
			'mount_point_file_type_3',
			'mount_point_file_type_4',
			'mount_point_file_type_5',
		);

		$result = TRUE;
		foreach ($fields as $fieldName) {
			// TRUE means the storage has data and thus was configured / saved once.
			if (!empty($storageRecord[$fieldName])) {
				$result = FALSE;
				break;
			}
		}
		return $result;
	}

	/**
	 * Format a message whenever the storage is offline.
	 *
	 * @return string
	 */
	protected function formatMessageStorageNotConfigured() {

		$storage = StorageUtility::getInstance()->getCurrentStorage();

		$result = <<< EOF
			<div class="typo3-message message-warning">
				<div class="message-header">
						Storage is not configured
				</div>
				<div class="message-body">
					The storage "{$storage->getName()}" looks currently not configured. Open the storage record "{$storage->getName()}"
					and assign some value in tab "Upload Settings" or "Default mount points.
				</div>
			</div>
EOF;

		return $result;
	}

	/**
	 * Check whether the storage is online or not.
	 *
	 * @return boolean
	 */
	protected function checkStorageOffline() {
		return !StorageUtility::getInstance()->getCurrentStorage()->isOnline();
	}

	/**
	 * Format a message whenever the storage is offline.
	 *
	 * @return string
	 */
	protected function formatMessageStorageOffline() {

		$storage = StorageUtility::getInstance()->getCurrentStorage();

		$result = <<< EOF
			<div class="typo3-message message-warning">
					<div class="message-header">
						Storage is currently offline
				</div>
					<div class="message-body">
						The storage "{$storage->getName()}" looks currently to be off-line. Contact an administrator if you think this is an error.
					</div>
				</div>
			</div>
EOF;

		return $result;
	}

	/**
	 * Check whether mount points privilege are ok.
	 *
	 * @return boolean
	 */
	protected function checkMountPoints() {
		if (! $this->getBackendUser()->isAdmin()) {

			$fileMounts = $this->getBackendUser()->getFileMountRecords();

			$fileMountIdentifiers = array();
			foreach ($fileMounts as $fileMount) {
				$fileMountIdentifiers[] = $fileMount['uid'];
			}

			$storage = StorageUtility::getInstance()->getCurrentStorage();
			$storageRecord = $storage->getStorageRecord();
			$fieldNames = array(
				'mount_point_file_type_1',
				'mount_point_file_type_2',
				'mount_point_file_type_3',
				'mount_point_file_type_4',
				'mount_point_file_type_5',
			);
			foreach ($fieldNames as $fileName) {
				$fileMountIdentifier = (int) $storageRecord[$fileName];
				if ($fileMountIdentifier > 0 && ! in_array($fileMountIdentifier, $fileMountIdentifiers)) {
					$this->notAllowedMountPoints[] = $this->fetchMountPoint($fileMountIdentifier);
				} else {
					# $fileMountIdentifier
					$folder = $storage->getRootLevelFolder();
				}
			}
		}
		return empty($this->notAllowedMountPoints);
	}

	/**
	 * Return a mount point according to an file mount identifier.
	 *
	 * @param string $identifier
	 * @return array
	 */
	protected function fetchMountPoint($identifier) {
		return $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'sys_filemounts', 'uid = ' . $identifier);
	}

	/**
	 * Format a message whenever mount points privilege are not OK.
	 *
	 * @return string
	 */
	protected function formatMessageMountPoints() {

		$storage = StorageUtility::getInstance()->getCurrentStorage();
		$backendUser = $this->getBackendUser();

		foreach ($this->notAllowedMountPoints as $notAllowedMountPoints) {
			$list = sprintf('<li>"%s" with path %s</li>',
				$notAllowedMountPoints['title'],
				$notAllowedMountPoints['path']
			);

		}

		$result = <<< EOF
			<div class="typo3-message message-warning">
					<div class="message-header">
						File mount are wrongly configured for user "{$backendUser->user['username']}".
				</div>
					<div class="message-body">
						User "{$backendUser->user['username']}" has no access to the following mount point configured in storage "{$storage->getName()}":
						<ul>
						{$list}
						</ul>
					</div>
				</div>
			</div>
EOF;

		return $result;
	}

	/**
	 * Returns an instance of the current Backend User.
	 *
	 * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
	 */
	protected function getBackendUser() {
		return $GLOBALS['BE_USER'];
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}
