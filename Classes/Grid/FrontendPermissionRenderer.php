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
 * Class rendering permission in the grid.
 */
class FrontendPermissionRenderer extends ColumnRendererAbstract
{

    /**
     * Render permission in the grid.
     *
     * @return string
     */
    public function render()
    {
        $result = '';

        $frontendUserGroups = $this->object['metadata']['fe_groups'];
        if (!empty($frontendUserGroups)) {

            /** @var $frontendUserGroup \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup */
            foreach ($frontendUserGroups as $frontendUserGroup) {
                $result .= sprintf('<li style="list-style: disc">%s</li>', $frontendUserGroup['title']);
            }
            $result = sprintf('<ul>%s</ul>', $result);
        }
        return $result;
    }
}
