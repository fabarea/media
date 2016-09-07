<?php
namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a "download" button to be placed in the grid.
 */
class DownloadButton extends AbstractComponentView
{

    /**
     * Renders a "download" button to be placed in the grid.
     *
     * @param Content $object
     * @return string
     * @throws \InvalidArgumentException
     */
    public function render(Content $object = null)
    {

        $button = $this->makeLinkButton()
            ->setHref($this->getDownloadUri($object))
            ->setDataAttributes([
                'uid' => $object->getUid(),
                'toggle' => 'tooltip',
            ])
            ->setClasses('btn-download')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:download'))
            ->setIcon($this->getIconFactory()->getIcon('actions-system-extension-download', Icon::SIZE_SMALL))
            ->render();

        return $button;
    }

    /**
     * @param Content $object
     * @return string
     */
    protected function getDownloadUri(Content $object)
    {
        $urlParameters = [
            MediaModule::getParameterPrefix() => [
                'controller' => 'Asset',
                'action' => 'download',
                'forceDownload' => true,
                'file' => $object->getUid(),
            ],
        ];
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }

}
