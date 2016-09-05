<?php
namespace Fab\Media;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for handling a dimension. The constructor takes a value such as "100x100" and split it in two parts: width and height.
 */
class Dimension
{

    /**
     * @var int
     */
    protected $width = 0;

    /**
     * @var int
     */
    protected $height = 0;

    /**
     * @param string $dimension
     */
    public function __construct($dimension)
    {
        $dimensions = GeneralUtility::trimExplode('x', $dimension);
        $this->width = empty($dimensions[0]) ? 0 : $dimensions[0];
        $this->height = empty($dimensions[1]) ? 0 : $dimensions[1];
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
