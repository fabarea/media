<?php
namespace TYPO3\CMS\Media\Command;

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

use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command Controller which handles actions related to Media.
 */
class MediaCommandController extends CommandController {

	/**
	 * @var array
	 */
	protected $message = array();

	/**
	 * @var array
	 */
	protected $missingFiles = array();

	/**
	 * @var array
	 */
	protected $duplicateFiles = array();

	/**
	 * @var \TYPO3\CMS\Core\Mail\MailMessage
	 */
	protected $mailMessage;

	/**
	 * Check whether the Index is Ok. In case not, display a message.
	 *
	 * @return void
	 */
	public function analyseIndexCommand() {

		foreach ($this->getStorageRepository()->findAll() as $storage) {

			$missingFiles = $this->getIndexAnalyser()->searchForMissingFiles($storage);

			$this->printOut();
			$this->printOut(sprintf('%s (%s)', $storage->getName(), $storage->getUid()));
			$this->printOut('--------------------------------------------');
			if (empty($missingFiles)) {
				$this->printOut();
				$this->printOut('Looks good, no missing files!');
			} else {
				// Missing files...
				$this->printOut();
				$this->printOut('Missing resources:');
				$this->missingFiles[$storage->getUid()] = $missingFiles; // Store missing files.

				/** @var \TYPO3\CMS\Core\Resource\File $missingFile */
				foreach ($missingFiles as $missingFile) {
					$message = sprintf('* Missing file "%s" with identifier "%s".',
						$missingFile->getUid(),
						$missingFile->getIdentifier()
					);
					$this->printOut($message);
				}
			}

			$duplicateFiles = $this->getIndexAnalyser()->searchForDuplicatesFiles($storage);

			// Duplicate file object
			if (empty($duplicateFiles)) {
				$this->printOut();
				$this->printOut('Looks good, no duplicate files!');
			} else {
				$this->printOut();
				$this->printOut('Duplicated identifiers detected:');
				$this->duplicateFiles[$storage->getUid()] = $duplicateFiles; // Store duplicate files.

				foreach ($duplicateFiles as $identifier => $duplicate) {

					// build temporary array
					$uids = array();
					foreach ($duplicate as $value) {
						$uids[] = $value['uid'];
					}

					$message = sprintf('* uids "%s" having same identifier %s',
						implode(',', $uids),
						$identifier
					);
					$this->printOut($message);

				}
			}
		}

		$this->sendReport();
	}

	/**
	 * Print a message and store its content in a variable for the email report.
	 *
	 * @param string $message
	 * @return void
	 */
	protected function printOut($message = '') {
		$this->message[] = $message;
		$this->outputLine($message);
	}

	/**
	 * Send a possible report to an admin.
	 *
	 * @throws \Exception
	 * @return void
	 */
	protected function sendReport() {
		if ($this->hasReport()) {

			// Prepare email.
			$this->getMailMessage()->setTo($this->getTo())
				->setFrom($this->getFrom())
				->setSubject('Missing files detected!')
				->setBody(implode("\n", $this->message));

			$isSent = $this->getMailMessage()->send();

			if (!$isSent) {
				throw new \Exception('I could not send a message', 1408343882);
			}

			$to = $this->getTo();
			$this->outputLine();
			$message = sprintf('Report was sent to %s', key($to));
			$this->outputLine($message);
		}
	}

	/**
	 * Send a report
	 *
	 * @return bool
	 */
	protected function hasReport() {
		return !empty($this->missingFiles) || !empty($this->duplicateFiles);
	}

	/**
	 * @return string
	 */
	protected function getTo() {

		// @todo make me more flexible!
		$emailAddress = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
		$name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
		$to[$emailAddress] = $name;
		return $to;
	}

	/**
	 * @return array
	 */
	protected function getFrom() {

		// @todo make me more flexible!
		$emailAddress = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
		$name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
		$from[$emailAddress] = $name;
		return $from;
	}

	/**
	 * @return StorageRepository
	 */
	protected function getStorageRepository() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
	}

	/**
	 * @return \TYPO3\CMS\Core\Mail\MailMessage
	 */
	public function getMailMessage() {
		if (is_null($this->mailMessage)) {
			$this->mailMessage = GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
		}
		return $this->mailMessage;
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Media\Index\IndexAnalyser
	 */
	protected function getIndexAnalyser() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Index\IndexAnalyser');
	}
}
