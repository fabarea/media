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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * Controller which handles actions related to Storage.
 */
class StorageController extends ActionController {

	/**
	 * @var string
	 */
	protected $tableName = 'sys_file_storage';

	/**
	 * @return void
	 */
	public function listAction() {
		$this->view->assign('storages', \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorages());
		$this->view->assign('storageTitle', TcaService::table($this->tableName)->getTitle());
		$this->view->assign('moduleUrl', BackendUtility::getModuleUrl('user_MediaM1'));
	}
}
