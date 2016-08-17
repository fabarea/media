<?php
namespace Fab\Media\ViewHelpers\File;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which returns property value of a file given by the context.
 */
class PropertyViewHelper extends AbstractViewHelper
{

    /**
     * Returns a property value of a file given by the context.
     *
     * @param string $name
     * @return string
     */
    public function render($name)
    {

        /** @var File $file */
        $file = $this->templateVariableContainer->get('file');
        return $file->getProperty($name);
    }
}
