<?php
namespace Fab\Media\Override\Backend\Form;

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

use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FormResultCompiler
 */
class FormResultCompiler extends \TYPO3\CMS\Backend\Form\FormResultCompiler
{

    /**
     * JavaScript bottom code
     *
     * @param string $formname The identification of the form on the page.
     * @return string A section with JavaScript - if $update is FALSE, embedded in <script></script>
     */
    protected function JSbottom($formname = 'forms[0]')
    {

        $out = parent::JSbottom($formname);

        $enableMediaFilePicker = (bool)$this->getBackendUser()->getTSConfigVal('options.vidi.enableMediaFilePicker');
        if ($enableMediaFilePicker) {

            $pageRenderer = $this->getPageRenderer();
            $pageRenderer->loadRequireJsModule('TYPO3/CMS/Media/MediaFormEngine', 'function(MediaFormEngine) {
            MediaFormEngine.vidiModuleUrl = \'' . BackendUtility::getModuleUrl(VidiModule::getSignature()) . '\';
            MediaFormEngine.vidiModulePrefix = \'' . VidiModule::getParameterPrefix() . '\';
            MediaFormEngine.browserUrl = ' . GeneralUtility::quoteJSvalue(BackendUtility::getModuleUrl('wizard_element_browser')) . ';
        }');
        }

        return $out;
    }

    /**
     * Returns an instance of the current Backend User.
     *
     * @return \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }
}
