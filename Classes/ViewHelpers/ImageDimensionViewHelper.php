<?php
namespace Fab\Media\ViewHelpers;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Media\Utility\ImagePresetUtility;

/**
 * View helper which returns default preset values related to an image dimension
 */
class ImageDimensionViewHelper extends AbstractViewHelper
{

    /**
     * Returns preset values related to an image dimension
     *
     * @param string $preset
     * @param string $dimension
     * @return int
     */
    public function render($preset, $dimension = 'width')
    {
        $imageDimension = ImagePresetUtility::getInstance()->preset($preset);
        if ($dimension == 'width') {
            $result = $imageDimension->getWidth();
        } else {
            $result = $imageDimension->getHeight();
        }
        return $result;
    }
}
