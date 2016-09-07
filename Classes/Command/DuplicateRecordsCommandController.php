<?php
namespace Fab\Media\Command;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command Controller which handles actions related to File Index.
 */
class DuplicateRecordsCommandController extends CommandController
{

    /**
     * @var array
     */
    protected $message = [];

    /**
     * @var array
     */
    protected $duplicateRecords = [];

    /**
     * @var \TYPO3\CMS\Core\Mail\MailMessage
     */
    protected $mailMessage;

    /**
     * Check whether the Index is Ok. In case not, display a message on the console.
     *
     * @return void
     */
    public function analyseCommand()
    {

        foreach ($this->getStorageRepository()->findAll() as $storage) {

            // For the CLI cause.
            $storage->setEvaluatePermissions(false);

            $this->printOut();
            $this->printOut(sprintf('%s (%s)', $storage->getName(), $storage->getUid()));
            $this->printOut('--------------------------------------------');

            if ($storage->isOnline()) {

                $duplicateRecords = $this->getIndexAnalyser()->searchForDuplicateIdentifiers($storage);

                // Duplicate file object
                if (empty($duplicateRecords)) {
                    $this->printOut();
                    $this->printOut('Looks good, no duplicate records!');
                } else {
                    $this->printOut();
                    $this->printOut('Duplicated identifiers detected:');
                    $this->duplicateRecords[$storage->getUid()] = $duplicateRecords; // Store duplicate files.

                    foreach ($duplicateRecords as $identifier => $duplicate) {

                        // build temporary array
                        $uids = [];
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
     * Print a message and store its content in a variable for the email report.
     *
     * @param string $message
     * @return void
     */
    protected function printOut($message = '')
    {
        $this->message[] = $message;
        $this->outputLine($message);
    }

    /**
     * Send a possible report to an admin.
     *
     * @throws \Exception
     * @return void
     */
    protected function sendReport()
    {
        if ($this->hasReport()) {

            // Prepare email.
            $this->getMailMessage()->setTo($this->getTo())
                ->setFrom($this->getFrom())
                ->setSubject('Duplicate records detected!')
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
    protected function hasReport()
    {
        return !empty($this->duplicateRecords);
    }

    /**
     * @return array
     */
    protected function getTo()
    {

        $to = [];

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
    protected function getFrom()
    {

        $from = [];

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
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * @return StorageRepository
     */
    protected function getStorageRepository()
    {
        return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
    }

    /**
     * @return \TYPO3\CMS\Core\Mail\MailMessage
     */
    public function getMailMessage()
    {
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
    protected function getIndexAnalyser()
    {
        return GeneralUtility::makeInstance('Fab\Media\Index\IndexAnalyser');
    }

}
