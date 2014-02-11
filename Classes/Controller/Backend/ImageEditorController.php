<?php
namespace TYPO3\CMS\Media\Controller\Backend;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException;
use TYPO3\CMS\Core\Resource\Exception\IllegalFileExtensionException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientUserPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\UploadException;
use TYPO3\CMS\Core\Resource\Exception\UploadSizeException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\FileUpload\UploadedFileInterface;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Utility\ConfigurationUtility;

/**
 * Controller which handles actions related to Image Editor.
 */
class ImageEditorController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 * @inject
	 */
	protected $assetRepository;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 * @inject
	 */
	protected $variantRepository;

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
	 * @param int $variant a possible variant can be given.
	 * @return void
	 */
	public function showAction($asset, $variant = 0) {
		$assetObject = $this->assetRepository->findByUid($asset);

		$variantObject = NULL;
		if ($variant > 0) {
			$variantObject = $this->variantRepository->findOneByVariant($variant);
		}

		$this->view->assign('asset', $assetObject);
		$this->view->assign('variant', $variantObject);
	}
}
