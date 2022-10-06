<?php

namespace Fab\Media\View;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which can output metadata of a file.
 * Give as input a template containing the metadata properties to render, example:
 *
 * $template = '%width x %height';
 * $fileProperties = array('width', 'height');
 */
class MetadataView extends AbstractViewHelper
{
    /**
     * Returns metadata according to a template.
     */
    public function render(File $file, $template = '', array $metadataProperties = array('size', 'width', 'height'), $configuration = []): string
    {
        if (empty($template)) {
            $template = $this->getDefaultTemplate($file);
        }
        $result = $template;
        foreach ($metadataProperties as $metadataProperty) {
            $value = $file->getProperty($metadataProperty);
            if ($metadataProperty === 'size') {
                $sizeUnit = empty($configuration['sizeUnit']) ? 1000 : $configuration['sizeUnit'];
                $value = round($file->getSize() / $sizeUnit);
            }
            $result = str_replace('%' . $metadataProperty, $value, $result);
        }
        return $result;
    }

    /**
     * Returns a default template.
     *
     * @param File $file
     * @return string
     */
    protected function getDefaultTemplate(File $file)
    {
        $template = '%size KB';

        if ($file->getType() == File::FILETYPE_IMAGE) {
            $template = '%width x %height - ' . $template;
        }

        return $template;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('file', File::class, '', true);
        $this->registerArgument('template', 'string', '', false, '');
        $this->registerArgument('metadataProperties', 'array', '', false, ['size', 'width', 'height']);
        $this->registerArgument('configuration', 'array', '', false, []);
    }
}
