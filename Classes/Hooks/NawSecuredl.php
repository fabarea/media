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
 *
 * @package media
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
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
	 * @param mixed $params array('pObj' => $pObj)
	 * @param \tx_nawsecuredl_output $secureDownload
	 */
	public function preOutput($params, $secureDownload) {

		$file = GeneralUtility::_GP('file');

		$storage = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorage();
		$rootFolder = $storage->getRootLevelFolder()->getPublicUrl();

		/** @var \TYPO3\CMS\Media\Domain\Repository\AssetRepository $assetRepository */
		$assetRepository = $this->objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');

		// Remove the segment from mount point
		$identifier = str_replace($rootFolder, '', $file);

		// Makes sure the identifier start with a slash
		$identifier = '/' . ltrim($identifier, '/');

		/** @var \TYPO3\CMS\Media\Domain\Model\Asset $asset */
		$asset = $assetRepository->findOneByIdentifier($identifier);

		if ($asset) {

			/** @var \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication $user */
			$user = \TYPO3\CMS\Frontend\Utility\EidUtility::initFeUser();
			if ($user->user !== FALSE) {
				$hasAccess = FALSE;
				$userGroups = explode(',', $user->user['usergroup']);

				/** @var \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup $group */
				foreach ($asset->getFrontendUserGroups() as $frontendGroup) {
					if (in_array($frontendGroup->getUid(), $userGroups)) {
						$hasAccess = TRUE;
						break;
					}
				}

				// No access
				if (!$hasAccess) {
					header('HTTP/1.0 403 Forbidden');
					die("Accessing the resource is forbidden!");
				}

			// when groups are set user needs to be logged-in
			}
			# @todo find a better way enabling file download when no permission applies. Envisaged solution http://forge.typo3.org/issues/48485
			#else {
			#	header('HTTP/1.0 401 Unauthorized');
			#	die("Accessing the resource requires authentication!");
			#}
		}
	}
}