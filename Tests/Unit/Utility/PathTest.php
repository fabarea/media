<?php
namespace Fab\Media\Utility;

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

/**
 * Test case for class \Fab\Media\Utility\Path.
 */
class PathTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	public function setUp() {
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function canResolvesAPath() {
		$resourceName = uniqid('resource');
		$expected = 'media/Resources/Public/' . $resourceName;
		$actual = \Fab\Media\Utility\Path::resolvePath($resourceName);

		$this->assertTrue(strpos($actual, $expected) > 0);
		$this->assertEquals(0, strpos(PATH_site, $expected));
	}

	/**
	 * @test
	 */
	public function canReturnsAPublicPath() {

		$resourceName = uniqid('resource');
		$expected = 'media/Resources/Public/' . $resourceName;
		$actual = \Fab\Media\Utility\Path::getRelativePath($resourceName);

		$this->assertTrue(strpos($actual, $expected) > 0);
		$this->assertFalse(strpos(PATH_site, $expected));
	}

	/**
	 * @test
	 */
	public function methodExistsReturnTrueForFileExistingInExtensionMedia() {
		$this->assertTrue(\Fab\Media\Utility\Path::exists('Icons/MissingMimeTypeIcon.png'));
	}

	/**
	 * @test
	 */
	public function methodNotExistsReturnFalseForFileExistingInExtensionMedia() {
		$this->assertFalse(\Fab\Media\Utility\Path::notExists('Icons/MissingMimeTypeIcon.png'));
	}

	/**
	 * @test
	 */
	public function returnsCanonicalPathForPathContainingRelativeSegment() {
		$actual = '../bar/../../foo.png';
		$expected = 'bar/foo.png';
		$this->assertSame($expected, Path::canonicalPath($actual));
	}
}
?>