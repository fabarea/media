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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a "edit" button to be placed in the grid.
 */
class EditButton extends AbstractComponentView
{

    /**
     * Renders a "edit" button to be placed in the grid.
     *
     * @param Content $object
     * @return string
     */
    public function render(Content $object = NULL)
    {
        $file = $this->getFileConverter()->convert($object);
        $metadataProperties = $file->_getMetaData();

        $button = $this->makeLinkButton()
            ->setHref($this->getUri($file))
            ->setDataAttributes([
                'uid' => $metadataProperties['uid'],
                'toggle' => 'tooltip',
            ])
            ->setClasses('btn-edit')
            ->setTitle($this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:edit_metadata'))
            ->setIcon($this->getIconFactory()->getIcon('actions-document-open', Icon::SIZE_SMALL))
            ->render();

        return $button;
    }

    /**
     * @param File $file
     * @return string
     */
    protected function getUri(File $file)
    {
        $metadataProperties = $file->_getMetaData();

        $parameterName = sprintf('edit[sys_file_metadata][%s]', $metadataProperties['uid']);
        $uri = BackendUtility::getModuleUrl(
            'record_edit',
            array(
                $parameterName => 'edit',
                'returnUrl' => BackendUtility::getModuleUrl(GeneralUtility::_GP('M'), $this->getAdditionalParameters())
            )
        );
        return $uri;
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
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }

}
