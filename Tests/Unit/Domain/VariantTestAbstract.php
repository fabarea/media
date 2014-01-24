<?php
namespace TYPO3\CMS\Media\Domain;

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
use TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase;

/**
 * Test case for class variant
 */
abstract class VariantTestAbstract extends BaseTestCase {

	/**
	 * @var int
	 */
	protected $fakeOriginalResourceUid;

	/**
	 * @var int
	 */
	protected $fakeVariantResourceUid;

	/**
	 * @var int
	 */
	protected $lastInsertedUid;

	/**
	 * @var \Tx_Phpunit_Framework
	 */
	protected $testingFramework;

	/**
	 * Populate DB with default records for sys_file
	 */
	protected function populateFileTable() {

		$this->fakeOriginalResourceUid = $this->testingFramework->createRecord(
			'sys_file',
			array(
				'identifier' => 'fake-identifier-file',
				'type' => 1,
				'name' => 'fake-name-file',
			)
		);

		$this->fakeVariantResourceUid = $this->testingFramework->createRecord(
			'sys_file',
			array(
				'identifier' => 'fake-identifier-variant',
				'type' => 1,
				'is_variant' => 1,
				'name' => 'fake-name-variant',
			)
		);
	}

	/**
	 * Populate DB with default records for sys_file_variants
	 */
	protected function populateVariantTable() {

		$this->lastInsertedUid = $this->testingFramework->createRecord(
			'sys_file_variants',
			array(
				'role' => 1,
				'original_resource' => $this->fakeOriginalResourceUid,
				'variant_resource' => $this->fakeVariantResourceUid,
			)
		);
	}
}
?>