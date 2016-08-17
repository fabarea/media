<?php
namespace Fab\Media\Grid;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Grid\ColumnRendererAbstract;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for rendering a configurable metadata property of a file in the Grid.
 */
class ActionPermissionColumn extends ColumnRendererAbstract
{

    /**
     * Renders a configurable metadata property of a file in the Grid.
     *
     * @throws \Exception
     * @return string
     */
    public function render()
    {

        $file = $this->getFileConverter()->convert($this->object);
        $permission = '';

        if ($file->checkActionPermission('read')) {
            $permission = 'R';
        }
        if ($file->checkActionPermission('write')) {
            $permission .= 'W';
        }

        return '<strong>' . $permission . '</strong>';
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }

}
