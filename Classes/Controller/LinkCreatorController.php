<?php
namespace Fab\Media\Controller;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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
