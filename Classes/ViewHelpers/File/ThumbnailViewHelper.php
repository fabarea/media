<?php
namespace Fab\Media\ViewHelpers\File;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Media\Utility\ImagePresetUtility;
use Fab\Vidi\Domain\Model\Content;

/**
 * View helper which returns a configurable thumbnail for a File.
 */
class ThumbnailViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('file', 'mixed', 'The source file', false, null);
        $this->registerArgument('configuration', 'array', 'Configuration to be given for the thumbnail processing.', false, []);
        $this->registerArgument('attributes', 'array', 'DOM attributes to add to the thumbnail image', false, '');
        $this->registerArgument('preset', 'string', 'Image dimension preset', false, '');
        $this->registerArgument('output', 'string', 'Can be: uri, image, imageWrapped', false, 'image');
        $this->registerArgument('configurationWrap', 'array', 'The configuration given to the wrap.', false, '');
    }

    /**
     * Returns a configurable thumbnail of an asset
     *
     * @throws \Exception
     * @return string
     */
    public function render()
    {

        $file = $this->arguments['file'];
        $preset = $this->arguments['preset'];
        $configuration = $this->arguments['configuration'];
        if (!is_array($configuration)) {
            $configuration = array();
        }
        $configurationWrap = $this->arguments['configurationWrap'];
        $attributes = $this->arguments['attributes'];
        $output = $this->arguments['output'];

        if ($file instanceof Content) {
            $file = $this->getFileConverter()->convert($file);
        } elseif (!($file instanceof File)) {
            $file = ResourceFactory::getInstance()->getFileObject((int)$file);
        }
        if ($preset) {
            $imageDimension = ImagePresetUtility::getInstance()->preset($preset);
            $configuration['width'] = $imageDimension->getWidth();
            $configuration['height'] = $imageDimension->getHeight();
        }
        /** @var $thumbnailService \Fab\Media\Thumbnail\ThumbnailService */
        $thumbnailService = GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailService', $file);
        $thumbnail = $thumbnailService->setConfiguration($configuration)
            ->setConfigurationWrap($configurationWrap)
            ->setAttributes($attributes)
            ->setOutputType($output)
            ->create();

        return $thumbnail;
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }

}
