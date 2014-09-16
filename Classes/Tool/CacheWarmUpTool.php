<?php
namespace TYPO3\CMS\Media\Tool;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Vidi\Tool\AbstractTool;

/**
 * Relation Analyser for a Vidi module.
 */
class CacheWarmUpTool extends AbstractTool {

	/**
	 * Display the title of the tool on the welcome screen.
	 *
	 * @return string
	 */
	public function getTitle() {
		return 'Cache warm up';
	}

	/**
	 * Display the description of the tool in the welcome screen.
	 *
	 * @return string
	 */
	public function getDescription() {
		$templateNameAndPath = 'EXT:media/Resources/Private/Backend/StandAlone/Tool/CacheWarmUp/Launcher.html';
		$view = $this->initializeStandaloneView($templateNameAndPath);
		$view->assign('sitePath', PATH_site);
		return $view->render();
	}

	/**
	 * Do the job: warm up the cache.
	 *
	 * @param array $arguments
	 * @return string
	 */
	public function work(array $arguments = array()) {

		$templateNameAndPath = 'EXT:media/Resources/Private/Backend/StandAlone/Tool/CacheWarmUp/WorkResult.html';
		$view = $this->initializeStandaloneView($templateNameAndPath);


		$numberOfEntries = $this->getCacheService()->warmUp();
		$view->assign('numberOfEntries', $numberOfEntries);

		return $view->render();
	}

	/**
	 * Tell whether the tools should be displayed according to the context.
	 *
	 * @return bool
	 */
	public function isShown() {
		return $this->getBackendUser()->isAdmin();
	}

	/**
	 * @return \TYPO3\CMS\Media\Cache\CacheService
	 */
	protected function getCacheService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Cache\CacheService');
	}

}

