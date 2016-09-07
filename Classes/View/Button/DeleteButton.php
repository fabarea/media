<?php
namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a "delete" button to be placed in the grid.
 */
class DeleteButton extends AbstractComponentView
{

    /**
     * Renders a "delete" button to be placed in the grid.
     *
     * @param \Fab\Vidi\Domain\Model\Content $object
     * @return string
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function render(Content $object = null)
    {

        $button = '';
        $file = $this->getFileConverter()->convert($object);

        // Only display the delete icon if the file has no reference.
        if ($this->getFileReferenceService()->countTotalReferences($object->getUid()) === 0 && $file->checkActionPermission('write')) {

            $button = $this->makeLinkButton()
                ->setHref($this->getDeleteUri($object))
                ->setDataAttributes([
                    'uid' => $object->getUid(),
                    'toggle' => 'tooltip',
                    'label' => $file->getProperty('title'),
                ])
                ->setClasses('btn-delete')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/locallang_mod_web_list.xlf:delete'))
                ->setIcon($this->getIconFactory()->getIcon('actions-edit-delete', Icon::SIZE_SMALL))
                ->render();
        }

        return $button;
    }

    /**
     * @param Content $object
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getDeleteUri(Content $object)
    {
        $additionalParameters = array(
            $this->getModuleLoader()->getParameterPrefix() => array(
                'controller' => 'Content',
                'action' => 'delete',
                'format' => 'json',
                'matches' => array(
                    'uid' => $object->getUid(),
                ),
            ),
        );
        return $this->getModuleLoader()->getModuleUrl($additionalParameters);
    }

    /**
     * @return \Fab\Media\Resource\FileReferenceService
     * @throws \InvalidArgumentException
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     * @throws \InvalidArgumentException
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }

}
