<?php
namespace TYPO3\CMS\Media\Override\Core\Resource;

/***************************************************************
 * Copyright notice
 *
 * (c) 2012-2013 Benjamin Mack <benni@typo3.org>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Representation of a specific processed version of a file. These are created by the FileProcessingService,
 * which in turn uses helper classes for doing the actual file processing. See there for a detailed description.
 *
 * Objects of this class may be freshly created during runtime or being fetched from the database. The latter
 * indicates that the file has been processed earlier and was then cached.
 *
 * Each processed file—besides belonging to one file—has been created for a certain task (context) and
 * configuration. All these won't change during the lifetime of a processed file; the only thing
 * that can change is the original file, or rather it's contents. In that case, the processed file has to
 * be processed again. Detecting this is done via comparing the current SHA1 hash of the original file against
 * the one it had at the time the file was processed.
 * The configuration of a processed file indicates what should be done to the original file to create the
 * processed version. This may include things like cropping, scaling, rotating, flipping or using some special
 * magic.
 * A file may also meet the expectations set in the configuration without any processing. In that case, the
 * ProcessedFile object still exists, but there is no physical file directly linked to it. Instead, it then
 * redirects most method calls to the original file object. The data of these objects are also stored in the
 * database, to indicate that no processing is required. With such files, the identifier and name fields in the
 * database are empty to show this.
 *
 * @author Benjamin Mack <benni@typo3.org>
 */
class ProcessedFile extends \TYPO3\CMS\Core\Resource\ProcessedFile {

	/**
	 * Injects a local file, which is a processing result into the object.
	 *
	 * @param string $filePath
	 * @return void
	 * @throws \RuntimeException
	 */
	public function updateWithLocalFile($filePath) {
		if ($this->identifier === NULL) {
			throw new \RuntimeException('Cannot update original file!', 1350582054);
		}

		// Code stolen from TYPO3 6.0.4 because of regression introduced in 6.0.5 and above http://forge.typo3.org/issues/47211
		$this->storage->addFile($filePath, $this->storage->getProcessingFolder(), $this->name, 'replace');
		// Update some related properties
		$this->originalFileSha1 = $this->originalFile->getSha1();
		$this->deleted = FALSE;
		$this->updated = TRUE;
	}
}

?>