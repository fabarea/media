<?php
namespace Fab\Media\Grid;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use Fab\Vidi\Grid\ColumnRendererAbstract;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Thumbnail\ThumbnailInterface;

/**
 * Class rendering the preview of a media in the Grid.
 */
class PreviewRenderer extends ColumnRendererAbstract
{

    /**
     * Render a preview of a file in the Grid.
     *
     * @return string
     */
    public function render()
    {

        $file = $this->getFileConverter()->convert($this->object);

        $uri = false;
        $appendTime = true;

        // Compute image-editor or link-creator URL.
        if ($this->getModuleLoader()->hasPlugin('imageEditor')) {
            $appendTime = false;
            $uri = $this->getPluginUri('ImageEditor');
        } elseif ($this->getModuleLoader()->hasPlugin('linkCreator')) {
            $appendTime = false;
            $uri = $this->getPluginUri('LinkCreator');
        }

        $result = $this->getThumbnailService($file)
            ->setOutputType(ThumbnailInterface::OUTPUT_IMAGE_WRAPPED)
            ->setAppendTimeStamp($appendTime)
            ->setTarget(ThumbnailInterface::TARGET_BLANK)
            ->setAnchorUri($uri)
            ->setAttributes([])
            ->create();

        // Add file info
        $result .= sprintf('<div class="container-fileInfo" style="font-size: 7pt; color: #777;">%s</div>',
            $this->getMetadataViewHelper()->render($file)
        );
        return $result;
    }

    /**
     * @param File $file
     * @return \Fab\Media\Thumbnail\ThumbnailService
     */
    protected function getThumbnailService(File $file)
    {
        return GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailService', $file);
    }

    /**
     * @return \Fab\Media\ViewHelpers\MetadataViewHelper
     */
    protected function getMetadataViewHelper()
    {
        return GeneralUtility::makeInstance('Fab\Media\ViewHelpers\MetadataViewHelper');
    }

    /**
     * @param string $controllerName
     * @return string
     */
    protected function getPluginUri($controllerName)
    {
        $urlParameters = array(
            MediaModule::getParameterPrefix() => array(
                'controller' => $controllerName,
                'action' => 'show',
                'file' => $this->object->getUid(),
            ),
        );
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }

}
