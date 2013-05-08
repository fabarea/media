<?php
namespace TYPO3\CMS\Media\Service\Thumbnail;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Media development team <typo3-project-media@lists.typo3.org>
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

/**
 * Test case for class \TYPO3\CMS\Media\Service\Thumbnail\FallBackThumbnail.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class FallBackThumbnailTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Service\Thumbnail\FallBackThumbnail
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Service\Thumbnail\FallBackThumbnail();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function canCreateThumbnail() {
		$expected = '<img src="../typo3conf/ext/media/Resources/Public/Icons/UnknownMimeType.png" hspace="2" class="" alt="" />';
		$this->assertEquals($expected, $this->fixture->create());
	}


	public function getMockMedia() {
		$data = array(
			'uid' => rand(10000, 100000),
			'identifier' => uniqid('/foo'),
		);
		return new \TYPO3\CMS\Media\Domain\Model\Asset($data);
	}
}
?>