<?php

namespace Fab\Media\Tests\Functional;

abstract class AbstractFunctionalTestCase extends \TYPO3\CMS\Core\Tests\FunctionalTestCase
{
    /** @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface The object manager */
    protected $objectManager;
    protected $testExtensionsToLoad = array('typo3conf/ext/vidi', 'typo3conf/ext/media');
    protected $coreExtensionsToLoad = array('extbase', 'fluid', 'scheduler');

    public function setUp()
    {
        parent::setUp();
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
    }

}
