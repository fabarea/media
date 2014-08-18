<?php
namespace TYPO3\CMS\Media\ViewHelpers\Form;

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

/**
 * View helper dealing with form footer.
 * @deprecated not used anymore!
 */
class FooterViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	public function __construct() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Render a form footer.
	 * Example:
	 * Created on 30-12-12 by John Updated on 22-05-12 by Jane
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $object Object to use for the form. Use in conjunction with the "property" attribute on the sub tags
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $object = NULL) {
		$template = '<span>%s %s %s</span> <span class="offset1">%s %s %s</span>';

		/** @var $dateViewHelper \TYPO3\CMS\Fluid\ViewHelpers\Format\DateViewHelper */
		$dateViewHelper = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Fluid\ViewHelpers\Format\DateViewHelper');

		$format = sprintf('%s @ %s',
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
		);

		$result = sprintf($template,
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('created_on', 'media'),
			$object->getProperty('crdate') ? $dateViewHelper->render('@' . $object->getProperty('crdate'), $format) : '',
			$this->getUserName($object->getProperty('cruser_id')),
			\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('updated_on', 'media'),
			$object->getProperty('tstamp') ? $dateViewHelper->render('@' . $object->getProperty('tstamp'), $format) : '',
			$this->getUserName($object->getProperty('upuser_id'))
		);

		return $result;
	}


	/**
	 * Get the User name to be displayed
	 *
	 * @param int $userUid
	 * @return string
	 */
	public function getUserName($userUid){

		$result = '';

		// @bug Raises an exception #1247602160: Table 'tx_beuser_domain_model_backenduser' doesn't exist:
		/** @var $backendRepository \TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository */
		#$backendRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository');
		/** @var $user \TYPO3\CMS\Extbase\Domain\Model\BackendUser */
		#$user = $backendRepository->findByUid($userId);

		if ($userUid > 0) {
			$record = $this->databaseHandler->exec_SELECTgetSingleRow('*', 'be_users', 'uid = ' . $userUid);
			$result = sprintf('%s %s',
				\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('by', 'media'),
				empty($record['realName']) ? $record['username'] : $record['realName']
			);
		}

		return $result;
	}
}
