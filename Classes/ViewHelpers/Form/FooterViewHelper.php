<?php
namespace Fab\Media\ViewHelpers\Form;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\Exception;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper dealing with form footer.
 */
class FooterViewHelper extends AbstractViewHelper
{

    /**
     * Render a form footer.
     * Example:
     * Created on 30-12-12 by John Updated on 22-05-12 by Jane
     *
     * @return string
     */
    public function render()
    {

        /** @var File $file */
        $file = $this->templateVariableContainer->get('file');
        $template = '<span>%s %s %s</span> <span style="padding-left: 50px">%s %s %s</span>';

        $format = sprintf('%s @ %s',
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'],
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['hhmm']
        );

        $arguments = array(
            'date' => null,
            'format' => $format,
            'base' => null
        );

        $result = sprintf($template,
            LocalizationUtility::translate('created_on', 'media'),
            $file->getProperty('crdate') ? $this->formatDate($arguments, $file->getProperty('crdate')) : '',
            $this->getUserName($file->getProperty('cruser_id')),
            LocalizationUtility::translate('updated_on', 'media'),
            $file->getProperty('tstamp') ? $this->formatDate($arguments, '@' . $file->getProperty('tstamp')) : '',
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
    public function getUserName($userIdentifier)
    {

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
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }


    /**
     * @param array $arguments
     * @param int|string $date
     * @return string
     * @throws Exception
     */
    public function formatDate(array $arguments, $date)
    {
        $format = $arguments['format'];
        $base = $date;
        if (is_string($base)) {
            $base = trim($base);
        }

        if ($format === '') {
            $format = $GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'] ?: 'Y-m-d';
        }

        if (is_string($date)) {
            $date = trim($date);
        }

        if ($date === '') {
            $date = 'now';
        }

        if (!$date instanceof \DateTimeInterface) {
            try {
                $base = $base instanceof \DateTimeInterface ? $base->format('U') : strtotime((MathUtility::canBeInterpretedAsInteger($base) ? '@' : '') . $base);
                $dateTimestamp = strtotime((MathUtility::canBeInterpretedAsInteger($date) ? '@' : '') . $date, $base);
                $date = new \DateTime('@' . $dateTimestamp);
                $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));
            } catch (\Exception $exception) {
                throw new Exception('"' . $date . '" could not be parsed by \DateTime constructor: ' . $exception->getMessage(), 1241722579);
            }
        }

        if (strpos($format, '%') !== false) {
            return strftime($format, $date->format('U'));
        } else {
            return $date->format($format);
        }
    }
}
