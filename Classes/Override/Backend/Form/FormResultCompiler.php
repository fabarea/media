<?php
namespace Fab\Media\Override\Backend\Form;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
     * @return string A section with JavaScript - if $update is false, embedded in <script></script>
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
