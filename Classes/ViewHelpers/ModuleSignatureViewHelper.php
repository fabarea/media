<?php
namespace Fab\Media\ViewHelpers;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper which outputs the BE module signature.
 */
class ModuleSignatureViewHelper extends AbstractViewHelper
{

    /**
     * Returns the BE module signature.
     *
     * @return string
     */
    public function render()
    {
        return VidiModule::getSignature();
    }
}
