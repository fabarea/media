<?php
namespace Fab\Media\Tests\Functional;

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

use TYPO3\CMS\Core\Tests\FunctionalTestCase;

/**
 * Class AbstractFunctionalTestCase
 */
abstract class AbstractFunctionalTestCase extends FunctionalTestCase {

	/** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager */
	protected $objectManager;

	protected $testExtensionsToLoad = array('typo3conf/ext/vidi', 'typo3conf/ext/media');

	protected $coreExtensionsToLoad = array('extbase', 'fluid', 'scheduler');

	public function setUp() {
		parent::setUp();
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
	}

}
