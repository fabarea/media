<?php
namespace Fab\Media\ViewHelpers\Form;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper dealing with form footer.
 */
class FooterViewHelper extends AbstractViewHelper {

	/**
	 * Render a form footer.
	 * Example:
	 * Created on 30-12-12 by John Updated on 22-05-12 by Jane
	 *
	 * @return string
	 */
	public function render() {

		/** @var File $file */
		$file = $this->templateVariableContainer->get('file');
		$template = '<span>%s %s %s</span> <span class="offset1">%s %s %s</span>';

		/** @var $dateViewHelper \TYPO3\CMS\Fluid\ViewHelpers\Format\DateViewHelper */
		$dateViewHelper = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\ViewHelpers\Format\DateViewHelper');

		$format = sprintf('%s @ %s',
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
		);

		$result = sprintf($template,
			LocalizationUtility::translate('created_on', 'media'),
			$file->getProperty('crdate') ? $dateViewHelper->render('@' . $file->getProperty('crdate'), $format) : '',
			$this->getUserName($file->getProperty('cruser_id')),
			LocalizationUtility::translate('updated_on', 'media'),
			$file->getProperty('tstamp') ? $dateViewHelper->render('@' . $file->getProperty('tstamp'), $format) : '',
			$this->getUserName($file->getProperty('upuser_id'))
		);

		return $result;
	}


	/**
	 * Get the User name to be displayed
	 *
	 * @param int $userIdentifier
	 * @return string
	 */
	public function getUserName($userIdentifier){

		$username = '';

		if ($userIdentifier > 0) {
			$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'be_users', 'uid = ' . $userIdentifier);
			$username = sprintf('%s %s',
				LocalizationUtility::translate('by', 'media'),
				empty($record['realName']) ? $record['username'] : $record['realName']
			);
		}

		return $username;
	}


	/**
	 * Returns a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}
}
