<?php

namespace Fab\Media\Backend;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Media\Form\FileUploadTceForms;
use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom fields for Media
 */
class TceForms extends AbstractFormElement
{
    /**
     * @return array
     */
    public function render()
    {
        // Load StyleSheets in the Page Renderer
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $cssFile = ExtensionManagementUtility::extPath('media') . 'Resources/Public/StyleSheets/fineuploader.tce.css';
        $pageRenderer->addCssFile($cssFile);

        // language labels for JavaScript files
        $pageRenderer->addInlineLanguageLabelFile(ExtensionManagementUtility::extPath('media') . 'Resources/Private/Language/locallang.xlf', 'media_file_upload');

        // js files to be loaded
        $pageRenderer->addJsFile(ExtensionManagementUtility::extPath('core') . 'Resources/Public/JavaScript/Contrib/jquery/jquery.min.js');
        $pageRenderer->addJsFile(ExtensionManagementUtility::extPath('media') . 'Resources/Public/JavaScript/Encoder.js');
        $pageRenderer->addJsFile(ExtensionManagementUtility::extPath('media') . 'Resources/Public/Libraries/Fineuploader/jquery.fineuploader-5.0.9.min.js');

        $result = $this->initializeResultArray();

        $fileMetadataRecord = $this->data['databaseRow'];

        if ($fileMetadataRecord['file'] <= 0) {
            throw new \Exception('I could not find a valid file identifier', 1392926871);
        }

        /** @var $fileUpload \Fab\Media\Form\FileUploadTceForms */
        $fileUpload = GeneralUtility::makeInstance(FileUploadTceForms::class);
        $fileUpload->setValue($fileMetadataRecord['file'][0])->setPrefix(MediaModule::getParameterPrefix());
        $result['html'] = $fileUpload->render();
        return $result;
    }
}
