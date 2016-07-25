<?php
namespace Fab\Media\Command;

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
 * Command Controller which handles actions related to File Index.
 */
class MissingFilesCommandController extends CommandController {

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
	protected $deletedFiles = array();

	/**
	 * @var \TYPO3\CMS\Core\Mail\MailMessage
	 */
	protected $mailMessage;

	/**
	 * Check whether the Index is Ok. In case not, display a message on the console.
	 *
	 * @return void
	 */
	public function analyseCommand() {

		foreach ($this->getStorageRepository()->findAll() as $storage) {

			// For the CLI cause.
			$storage->setEvaluatePermissions(FALSE);

			$this->printOut();
			$this->printOut(sprintf('%s (%s)', $storage->getName(), $storage->getUid()));
			$this->printOut('--------------------------------------------');

			if ($storage->isOnline()) {

				$missingFiles = $this->getIndexAnalyser()->searchForMissingFiles($storage);
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

			} else {
				$this->outputLine('Storage is offline!');
			}
		}

		$to = $this->getTo();
		if (!empty($to)) {
			$this->sendReport();
		}
	}

	/**
	 * Delete the missing files which have no file references 
	 *
	 * @return void
	 */
	public function deleteCommand() {

		foreach ($this->getStorageRepository()->findAll() as $storage) {

			// For the CLI cause.
			$storage->setEvaluatePermissions(FALSE);

			$this->printOut();
			$this->printOut(sprintf('%s (%s)', $storage->getName(), $storage->getUid()));
			$this->printOut('--------------------------------------------');

			if ($storage->isOnline()) {

				$deletedFiles = $this->getIndexAnalyser()->deleteMissingFiles($storage);
				if (empty($deletedFiles)) {
					$this->printOut();
					$this->printOut('No files deleted!');
				} else {
					// Missing files...
					$this->printOut();
					$this->printOut('Deleted Files:');
					/** @var \TYPO3\CMS\Core\Resource\File $deletedFile */
					foreach ($deletedFiles as $deletedFileUid => $deletedFileIdentifier) {
						$message = sprintf('* Deleted file "%s" with identifier "%s".',
										   $deletedFileUid,
										   $deletedFileIdentifier
						);
						$this->printOut($message);
					}
				}

			} else {
				$this->outputLine('Storage is offline!');
			}
		}
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
		return !empty($this->missingFiles);
	}

	/**
	 * @return array
	 */
	protected function getTo() {

		$to = array();

		// @todo make me more flexible!
		if (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
			$emailAddress = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
			$name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
			$to[$emailAddress] = $name;

		}
		return $to;
	}

	/**
	 * @return array
	 */
	protected function getFrom() {

		$from = array();

		// @todo make me more flexible!
		if (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
			$emailAddress = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
			$name = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
			$from[$emailAddress] = $name;
		}
		return $from;
	}

	/**
	 * Returns a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
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
	 * @return \Fab\Media\Index\IndexAnalyser
	 */
	protected function getIndexAnalyser() {
		return GeneralUtility::makeInstance('Fab\Media\Index\IndexAnalyser');
	}

}
