<?php
namespace TYPO3\CMS\Media\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Media\Domain\VariantTestAbstract;

require_once(PATH_site . 'typo3conf/ext/media/Tests/Unit/Domain/VariantTestAbstract.php');

/**
 * Test case for class \TYPO3\CMS\Media\Domain\Model\Variant.
 */
class VariantTest extends VariantTestAbstract {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Model\Variant
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $className = 'TYPO3\CMS\Media\Domain\Model\Variant';

	/**
	 * @var ResourceStorage
	 */
	protected $storageMock;

	public function setUp() {
		$this->testingFramework = new \Tx_Phpunit_Framework('sys_file');

		$this->storageMock = $this->getMock('TYPO3\CMS\Core\Resource\ResourceStorage', array(), array(), '', FALSE);
		$this->storageMock->expects($this->any())->method('getUid')->will($this->returnValue(5));

		// Populate the database with records
		$this->populateFileTable();
		$this->populateVariantTable();

		$this->fixture = new \TYPO3\CMS\Media\Domain\Model\Variant(array('uid' => $this->fakeVariantResourceUid), $this->storageMock);
		$this->fixture->setIndexable(FALSE);
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework, $this->storageMock);
	}

	/**
	 * @test
	 */
	public function canInstantiateVariant() {
		$fixture = new \TYPO3\CMS\Media\Domain\Model\Variant(array('fakeVariant'), $this->storageMock);
		$fixture->setIndexable(FALSE);
		$this->assertInstanceOf($this->className, $fixture);
	}

	/**
	 * @test
	 * @expectedException \Exception
	 */
	public function variantResourceWithoutOriginalResourceRaisesException() {
		$fixture = new \TYPO3\CMS\Media\Domain\Model\Variant(array('uid' => -1), $this->storageMock);
		$fixture->setIndexable(FALSE);
		$this->assertSame($this->fakeOriginalResourceUid, (int) $fixture->getOriginalResource()->getUid());
	}

	/**
	 * @test
	 */
	public function canGetOriginalResource() {
		$this->assertSame($this->fakeOriginalResourceUid, (int)$this->fixture->getOriginalResource()->getUid());
	}

	/**
	 * @test
	 * @dataProvider propertyProvider
	 */
	public function testProperty($propertyName, $value) {
		$setter = 'set' . ucfirst($propertyName);
		$getter = 'get' . ucfirst($propertyName);
		call_user_func_array(array($this->fixture, $setter), array($value));
		$this->assertEquals($value, call_user_func(array($this->fixture, $getter)));
	}

	/**
	 * Provider
	 */
	public function propertyProvider() {
		return array(
			array('variation', 'fake-variation-value'),
			array('role', 1),
		);
	}
}
?>