<?php
namespace Fab\Media\View\Button;

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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a button to create a new folder.
 */
class NewFolder extends AbstractComponentView
{

    /**
     * Renders a button to create a new folder.
     *
     * @param Content $object
     * @return string
     */
    public function render(Content $object = NULL)
    {
        $output = '';
        if ($this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin()) {

            $button = $this->makeLinkButton()
                ->setHref($this->getNewFolderUri())
                ->setTitle($this->getLabel())
                ->setIcon($this->getIconFactory()->getIcon('actions-document-new', Icon::SIZE_SMALL))
                ->render();

            $output = '<div style="float: left;">' . $button . '</div>';
        }
        return $output;
    }

    /**
     * @return string
     */
    protected function getLabel()
    {
        $label = $this->getLanguageService()->sL('LLL:EXT:lang/locallang_core.xlf:cm.new', TRUE);
        return $this->getLanguageService()->makeEntities($label);
    }

    /**
     * @return string
     */
    protected function getNewFolderUri()
    {
        return BackendUtility::getModuleUrl(
            'file_newfolder',
            array(
                'target' => $this->getCombineIdentifier(),
                'returnUrl' => $this->getReturnUrl(),
            )
        );
    }


    /**
     * @return string
     */
    protected function getCombineIdentifier()
    {
        $folder = $this->getMediaModule()->getCurrentFolder();
        return $folder->getCombinedIdentifier();
    }

    /**
     * @return string
     */
    protected function getReturnUrl()
    {
        $returnUrl = BackendUtility::getModuleUrl(
            GeneralUtility::_GP('M'),
            $this->getAdditionalParameters()
        );
        return $returnUrl;
    }

    /**
     * @return array
     */
    protected function getAdditionalParameters()
    {

        $additionalParameters = array();
        if (GeneralUtility::_GP('id')) {
            $additionalParameters = array(
                'id' => urldecode(GeneralUtility::_GP('id')),
            );
        }
        return $additionalParameters;
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
