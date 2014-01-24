<?php
namespace TYPO3\CMS\Media\Domain\Repository;

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

use TYPO3\CMS\Media\Domain\VariantTestAbstract;

require_once(PATH_site . 'typo3conf/ext/media/Tests/Unit/Domain/VariantTestAbstract.php');


/**
 * Test case for class \TYPO3\CMS\Media\Domain\Repository\VariantRepository.
 */
class VariantRepositoryTest extends VariantTestAbstract {

	/**
	 * @var int
	 */
	private $numberOfFakeRecords = 3;

	/**
	 * @var string
	 */
	private $variantClassName = 'TYPO3\CMS\Media\Domain\Model\Variant';


	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 */
	private $fixture;

	public function setUp() {
		$this->testingFramework = new \Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Repository\VariantRepository();

		// Populate the database with records
		$this->populateFileTable();
		$this->populateVariantTable();
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework);
	}

	/**
	 * @test
	 */
	public function findAllMethodReturnAtLeastTheNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, count($this->fixture->findAll()));
	}

	/**
	 * @test
	 */
	public function firstObjectOfFindAllIsOfTypeVariant() {
		$variants = $this->fixture->findAll();
		$this->assertInstanceOf($this->variantClassName, $variants[0]);
	}

	/**
	 * @test
	 */
	public function findAllMethodWithRawResultReturnAtLeastTheNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, count($this->fixture->setRawResult(TRUE)->findAll()));
	}

	/**
	 * @test
	 */
	public function countAllMethodReturnAtLeastTheNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, $this->fixture->countAll());
	}

	/**
	 * @test
	 */
	public function findByUidReturnsAVariantObject() {
		$this->assertInstanceOf($this->variantClassName, $this->fixture->findByUid($this->fakeVariantResourceUid));
	}

	/**
	 * @test
	 */
	public function findByOriginalUidReturnsAnArray() {
		$fileMock = $this->getMock('TYPO3\CMS\Core\Resource\File', array(), array(), '', FALSE);
		$fileMock->expects($this->any())->method('getUid')->will($this->returnValue($this->fakeOriginalResourceUid));
		$this->assertTrue(is_array($this->fixture->findByOriginalResource($fileMock)));
	}

	/**
	 * @test
	 */
	public function callMethodFindByOriginalUidAndTestWhetherFirstItemIsOfTypeVariant() {
		$fileMock = $this->getMock('TYPO3\CMS\Core\Resource\File', array(), array(), '', FALSE);
		$fileMock->expects($this->any())->method('getUid')->will($this->returnValue($this->fakeOriginalResourceUid));
		$variants = $this->fixture->findByOriginalResource($fileMock);
		$this->assertInstanceOf($this->variantClassName, $variants[0]);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException
	 */
	public function randomMagicMethodReturnsException() {
		$this->fixture->ASDFEFGByType();
	}
}
?>