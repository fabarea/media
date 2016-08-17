<?php
namespace Fab\Media\Form;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * A interface dealing with form
 */
interface FormFieldInterface
{

    /**
     * @return string
     */
    public function render();

    /**
     * @param string $template
     * @return \Fab\Media\Form\FormFieldInterface
     */
    public function setTemplate($template);
}
