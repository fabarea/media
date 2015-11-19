<?php
namespace Fab\Media\Grid;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
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
