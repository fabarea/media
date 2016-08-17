<?php
namespace Fab\Media\Grid;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Grid\ColumnRendererAbstract;

/**
 * Class rendering category list of an asset in the grid.
 */
class CategoryRenderer extends ColumnRendererAbstract
{

    /**
     * Renders category list of an asset in the grid.
     *
     * @return string
     */
    public function render()
    {
        $result = '';

        $categories = $this->object['metadata']['categories'];
        if (!empty($categories)) {

            /** @var $category \TYPO3\CMS\Extbase\Domain\Model\Category */
            foreach ($categories as $category) {
                $result .= sprintf('<li>%s</li>', $category['title']);
            }
            $result = sprintf('<ul class="category-list">%s</ul>', $result);
        }
        return $result;
    }

}
