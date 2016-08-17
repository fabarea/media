<?php
namespace Fab\Media\View;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a button for uploading assets.
 */
class InlineJavaScript extends AbstractComponentView
{


    /**
     * Renders a button for uploading assets.
     *
     * @return string
     */
    public function render()
    {
        $parameterPrefix = MediaModule::getParameterPrefix();
        $output = "
<script>

window.Media = window.Media || {};
Media.parameterPrefix = '${parameterPrefix}';

</script>";

        return $output;
    }


}
