<?php
namespace Fab\Media\Utility;

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