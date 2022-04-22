<?php
namespace Fab\Media\Tests\Functional;

use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
/**
 * Class AbstractFunctionalTestCase
 */
abstract class AbstractFunctionalTestCase extends FunctionalTestCase {

	/** @var ObjectManagerInterface The object manager */
	protected $objectManager;

	protected $testExtensionsToLoad = array('typo3conf/ext/vidi', 'typo3conf/ext/media');

	protected $coreExtensionsToLoad = array('extbase', 'fluid', 'scheduler');

	public function setUp() {
		parent::setUp();
		$this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
	}

}
