<?php
namespace Fab\Media\Controller;

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

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Link Creator.
 */
class LinkCreatorController extends ActionController
{

    /**
     * Initializes the controller before invoking an action method.
     */
    public function initializeAction()
    {

        // Configure property mapping to retrieve the file object.
        if ($this->arguments->hasArgument('file')) {

            /** @var \Fab\Media\TypeConverter\FileConverter $typeConverter */
            $typeConverter = $this->objectManager->get('Fab\Media\TypeConverter\FileConverter');

            $propertyMappingConfiguration = $this->arguments->getArgument('file')->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->setTypeConverter($typeConverter);
        }
    }

    /**
     * Handle GUI for creating a link in the RTE.
     *
     * @param File $file
     * @return void
     */
    public function showAction(File $file)
    {
        $this->view->assign('file', $file);
    }

}
