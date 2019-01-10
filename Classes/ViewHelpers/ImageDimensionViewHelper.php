<?php
namespace Fab\Media\ViewHelpers;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use Fab\Media\Utility\ImagePresetUtility;

/**
 * View helper which returns default preset values related to an image dimension
 */
class ImageDimensionViewHelper extends AbstractViewHelper
{

    /**
     * @return void
     */
    public function initializeArguments()
    {
        $this->registerArgument('preset', 'string', '', true);
        $this->registerArgument('dimension', 'string', '', false, 'width');
    }

    /**
     * Returns preset values related to an image dimension
     *
     * @return int
     */
    public function render()
    {
        $preset = $this->arguments['preset'];
        $dimension = $this->arguments['dimension'];

        $imageDimension = ImagePresetUtility::getInstance()->preset($preset);
        if ($dimension === 'width') {
            $result = $imageDimension->getWidth();
        } else {
            $result = $imageDimension->getHeight();
        }
        return $result;
    }
}
