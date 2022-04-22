<?php
namespace Fab\Media\Controller;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Psr\Http\Message\ResponseInterface;
use Fab\Media\TypeConverter\FileConverter;
use Fab\Media\Module\MediaModule;
use Fab\Vidi\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Image Editor.
 */
class ImageEditorController extends ActionController
{

    /**
     * Initializes the controller before invoking an action method.
     */
    public function initializeAction()
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addInlineLanguageLabelFile('EXT:media/Resources/Private/Language/locallang.xlf');

        // Configure property mapping to retrieve the file object.
        if ($this->arguments->hasArgument('file')) {

            /** @var FileConverter $typeConverter */
            $typeConverter = GeneralUtility::makeInstance(FileConverter::class);

            $propertyMappingConfiguration = $this->arguments->getArgument('file')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->setTypeConverter($typeConverter);
        }
    }

    /**
     * Handle GUI for inserting an image in the RTE.
     *
     * @param File $file
     * @return void
     */
    public function showAction(File $file): ResponseInterface
    {
        $this->view->assign('file', $file);
        $moduleSignature = MediaModule::getSignature();
        $this->view->assign('moduleUrl', BackendUtility::getModuleUrl($moduleSignature));
        return $this->htmlResponse();
    }

}
