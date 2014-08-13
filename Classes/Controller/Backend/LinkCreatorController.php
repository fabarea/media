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

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Link Creator.
 */
class LinkCreatorController extends ActionController {

	/**
	 * Handle GUI for creating a link in the RTE.
	 *
	 * @param int $asset
	 * @return void
	 */
	public function showAction($asset) {
		$file = ResourceFactory::getInstance()->getFileObject($asset);
		$this->view->assign('asset', $file);
	}
}
