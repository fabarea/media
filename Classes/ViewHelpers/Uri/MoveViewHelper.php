<?php
namespace Fab\Media\ViewHelpers\Uri;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which renders a move storage URI.
 */
class MoveViewHelper extends AbstractViewHelper
{

    /**
     * Render a move storage URI.
     *
     * @return string
     */
    public function render()
    {

        $urlParameters = array(
            VidiModule::getParameterPrefix() => array(
                'controller' => 'Content',
                'action' => 'move',
                'fieldNameAndPath' => $this->templateVariableContainer->get('fieldNameAndPath'),
                'matches' => $this->templateVariableContainer->get('matches'),
            ),
        );

        $moduleUrl = BackendUtility::getModuleUrl(
            VidiModule::getSignature(),
            $urlParameters
        );

        // Work around a bug in BackendUtility::getModuleUrl if matches is empty getModuleUrl() will not return the parameter.
        $matches = $this->templateVariableContainer->get('matches');
        if (empty($matches)) {
            $moduleUrl .= '&' . urlencode(VidiModule::getParameterPrefix() . '[matches]=');
        }

        return $moduleUrl;
    }

}
