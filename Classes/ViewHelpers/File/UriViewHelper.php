<?php
namespace Fab\Media\ViewHelpers\File;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Vidi\Domain\Model\Content;

/**
 * View helper which returns the File Uri.
 */
class UriViewHelper extends AbstractViewHelper
{

    /**
     * Returns a property value of a file given by the context.
     *
     * @param File|Content|int $file
     * @param bool $relative
     * @return string
     */
    public function render($file, $relative = false)
    {
        if (!$file instanceof File) {
            $file = $this->getFileConverter()->convert($file);
        }
        return $file->getPublicUrl($relative);
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }
}
