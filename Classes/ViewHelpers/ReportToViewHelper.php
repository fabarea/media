<?php
namespace Fab\Media\ViewHelpers;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns the people who will receive a report.
 */
class ReportToViewHelper extends AbstractViewHelper
{

    /**
     * Returns the people who will receive a report.
     *
     * @throws \Exception
     * @return string
     */
    public function render()
    {
        $reportTo = 'null (Missing value in $GLOBALS[TYPO3_CONF_VARS][MAIL][defaultMailFromAddress])';
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
            $reportTo = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
        }
        return $reportTo;
    }
}
