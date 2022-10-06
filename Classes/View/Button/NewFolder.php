<?php

namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use Fab\Vidi\Utility\BackendUtility;
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
    public function render($object = null)
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
        return $this->getLanguageService()->sL('LLL:EXT:lang/Resources/Private/Language/locallang_core.xlf:cm.new');
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
            GeneralUtility::_GP('route'),
            $this->getAdditionalParameters()
        );
        return $returnUrl;
    }

    /**
     * @return array
     */
    protected function getAdditionalParameters()
    {
        $additionalParameters = [];
        if (GeneralUtility::_GP('id')) {
            $additionalParameters = [
                'id' => urldecode(GeneralUtility::_GP('id')),
            ];
        }
        return $additionalParameters;
    }

    /**
     * @return MediaModule|object
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
