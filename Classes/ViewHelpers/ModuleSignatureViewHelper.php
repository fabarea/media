<?php
namespace Fab\Media\ViewHelpers;

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
