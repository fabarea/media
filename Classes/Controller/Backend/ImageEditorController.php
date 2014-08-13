<?php
namespace TYPO3\CMS\Media\Controller\Backend;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Image Editor.
 */
class ImageEditorController extends ActionController {

	/**
	 * @var \TYPO3\CMS\Core\Page\PageRenderer
	 * @inject
	 */
	protected $pageRenderer;

	/**
	 * @throws \TYPO3\CMS\Media\Exception\StorageNotOnlineException
	 */
	public function initializeAction() {
		$this->pageRenderer->addInlineLanguageLabelFile('EXT:media/Resources/Private/Language/locallang.xlf');
	}

	/**
	 * Handle GUI for inserting an image in the RTE.
	 *
	 * @param int $asset
	 * @return void
	 */
	public function showAction($asset) {
		$file = ResourceFactory::getInstance()->getFileObject($asset);
		$this->view->assign('asset', $file);
		$this->view->assign('moduleUrl', BackendUtility::getModuleUrl('user_MediaM1'));
	}
}
