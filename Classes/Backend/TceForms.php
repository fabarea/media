<?php
namespace Fab\Media\Backend;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Custom fields for Media
 */
class TceForms
{

    /**
     * Constructor
     */
    public function __construct()
    {

        // Load StyleSheets in the Page Renderer
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $cssFile = ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/StyleSheets/fineuploader.tce.css';
        $pageRenderer->addCssFile($cssFile);

        // language labels for JavaScript files
        $pageRenderer->addInlineLanguageLabelFile(ExtensionManagementUtility::extPath('media') . 'Resources/Private/Language/locallang.xlf', 'media_file_upload');

        // js files to be loaded
        $pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/JavaScript/Encoder.js');
        $pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/JavaScript/JQuery/jquery.fineuploader.compatibility.js');
        $pageRenderer->addJsFile(ExtensionManagementUtility::extRelPath('media') . 'Resources/Public/Libraries/Fineuploader/jquery.fineuploader-5.0.9.min.js');
    }

    /**
     * This method renders the user friendly upload widget.
     *
     * @param array $propertyArray : information related to the field
     * @param Object $tceForms : reference to calling TCEforms object
     * @throws \Exception
     * @return string
     */
    public function renderFileUpload($propertyArray, $tceForms)
    {

        $fileMetadataRecord = $propertyArray['row'];

        if ($fileMetadataRecord['file'] <= 0) {
            throw new \Exception('I could not find a valid file identifier', 1392926871);
        }

        /** @var $fileUpload \Fab\Media\Form\FileUploadTceForms */
        $fileUpload = GeneralUtility::makeInstance('Fab\Media\Form\FileUploadTceForms');
        $fileUpload->setValue($fileMetadataRecord['file'][0])->setPrefix(MediaModule::getParameterPrefix());
        return $fileUpload->render();
    }
}
