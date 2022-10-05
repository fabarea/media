<?php

namespace Fab\Media\ViewHelpers\File;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Media\TypeConverter\ContentToFileConverter;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * View helper which tell whether a file exists.
 */
class ExistsViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('file', 'mixed', '', true);
    }

    /**
     * Returns a property value of a file given by the context.
     *
     * @return bool
     */
    public function render()
    {
        /** @var File|Content|int $file $file */
        $file = $this->arguments['file'];

        if (!$file instanceof File) {
            $file = $this->getFileConverter()->convert($file);
        }

        return $file->exists();
    }

    /**
     * @return ContentToFileConverter|object
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance(ContentToFileConverter::class);
    }
}
