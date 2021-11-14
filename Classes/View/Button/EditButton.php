<?php
namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;
use Fab\Media\Module\VidiModule;

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
    public function render(Content $object = null)
    {
        $file = $this->getFileConverter()->convert($object);
        $metadataProperties = $file->getMetaData()->get();

        $button = '';
        if ($file->checkActionPermission('write')) {
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
        }

        return $button;
    }

    /**
     * @param File $file
     * @return string
     */
    protected function getUri(File $file)
    {
        $metadataProperties = $file->getMetaData()->get();

        $parameterName = sprintf('edit[sys_file_metadata][%s]', $metadataProperties['uid']);
        $uri = BackendUtility::getModuleUrl(
            'record_edit',
            array(
                $parameterName => 'edit',
                'returnUrl' => BackendUtility::getModuleUrl(GeneralUtility::_GP('route'), $this->getAdditionalParameters())
            )
        );
        return $uri;
    }

    /**
     * @return array
     */
    protected function getAdditionalParameters()
    {
        $additionalParameters = [];
        if (GeneralUtility::_GP('id')) {
            $additionalParameters['id'] = urldecode(GeneralUtility::_GP('id'));
        }

        $vidiParameters = GeneralUtility::_GP(VidiModule::PARAMETER_PREFIX);
        if (is_array($vidiParameters)) {
            $whitelistedParameters = array_intersect_key($vidiParameters, ['plugins' => true, 'matches' => true]);
            if (count($whitelistedParameters) === 2) {
                $additionalParameters[VidiModule::PARAMETER_PREFIX] = $whitelistedParameters;
            }
        }

        return $additionalParameters;
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter|object
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance(\Fab\Media\TypeConverter\ContentToFileConverter::class);
    }

}
