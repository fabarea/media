<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Media development team
 <typo3-project-media@lists.typo3.org>, TYPO3 Association
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
 * Test case for class tx_media.
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @package TYPO3
 * @subpackage tx_media
 *
 * @author Media development team
 <typo3-project-media@lists.typo3.org>
 */
class tx_mediaTest extends Tx_Extbase_Tests_Unit_BaseTestCase {
	/**
	 * @var tx_media
	 */
	protected $fixture;

	public function setUp() {
		$this->fixture = new tx_media();
	}

	public function tearDown() {
		unset($this->fixture);
	}
	
	
	/**
	 * @test
	 */
	public function getTitleReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() { 
		$this->fixture->setTitle('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getTitle()
		);
	}
	
	/**
	 * @test
	 */
	public function getDescriptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() { 
		$this->fixture->setDescription('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getDescription()
		);
	}
	
	/**
	 * @test
	 */
	public function getKeywordsReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setKeywordsForStringSetsKeywords() { 
		$this->fixture->setKeywords('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getKeywords()
		);
	}
	
	/**
	 * @test
	 */
	public function getMimeTypeReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setMimeTypeForStringSetsMimeType() { 
		$this->fixture->setMimeType('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getMimeType()
		);
	}
	
	/**
	 * @test
	 */
	public function getExtensionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setExtensionForStringSetsExtension() { 
		$this->fixture->setExtension('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getExtension()
		);
	}
	
	/**
	 * @test
	 */
	public function getCreationDateReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setCreationDateForDateTimeSetsCreationDate() { }
	
	/**
	 * @test
	 */
	public function getModificationDateReturnsInitialValueForDateTime() { }

	/**
	 * @test
	 */
	public function setModificationDateForDateTimeSetsModificationDate() { }
	
	/**
	 * @test
	 */
	public function getCreatorToolReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setCreatorToolForStringSetsCreatorTool() { 
		$this->fixture->setCreatorTool('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getCreatorTool()
		);
	}
	
	/**
	 * @test
	 */
	public function getDownloadNameReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setDownloadNameForStringSetsDownloadName() { 
		$this->fixture->setDownloadName('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getDownloadName()
		);
	}
	
	/**
	 * @test
	 */
	public function getIdentifierReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setIdentifierForStringSetsIdentifier() { 
		$this->fixture->setIdentifier('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getIdentifier()
		);
	}
	
	/**
	 * @test
	 */
	public function getCreatorReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setCreatorForStringSetsCreator() { 
		$this->fixture->setCreator('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getCreator()
		);
	}
	
	/**
	 * @test
	 */
	public function getSourceReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setSourceForStringSetsSource() { 
		$this->fixture->setSource('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getSource()
		);
	}
	
	/**
	 * @test
	 */
	public function getAlternativeReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setAlternativeForStringSetsAlternative() { 
		$this->fixture->setAlternative('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getAlternative()
		);
	}
	
	/**
	 * @test
	 */
	public function getCaptionReturnsInitialValueForString() { }

	/**
	 * @test
	 */
	public function setCaptionForStringSetsCaption() { 
		$this->fixture->setCaption('Conceived at T3CON10');

		$this->assertSame(
			'Conceived at T3CON10',
			$this->fixture->getCaption()
		);
	}
	
	/**
	 * @test
	 */
	public function getFalReturnsInitialValueForTx_Media_Domain_Model_File() {
		$this->assertEquals(
			NULL,
			$this->fixture->getFal()
		);
	}

	/**
	 * @test
	 */
	public function setFalForTx_Media_Domain_Model_FileSetsFal() {
		$dummyObject = new Tx_Media_Domain_Model_File();
		$this->fixture->setFal($dummyObject);

		$this->assertSame(
			$dummyObject,
			$this->fixture->getFal()
		);
	}
	
	/**
	 * @test
	 */
	public function getMediaTypeReturnsInitialValueFortx_mediaType() {
		$this->assertEquals(
			NULL,
			$this->fixture->getMediaType()
		);
	}

	/**
	 * @test
	 */
	public function setMediaTypeFortx_mediaTypeSetsMediaType() {
		$dummyObject = new tx_mediaType();
		$this->fixture->setMediaType($dummyObject);

		$this->assertSame(
			$dummyObject,
			$this->fixture->getMediaType()
		);
	}
	
}
?>