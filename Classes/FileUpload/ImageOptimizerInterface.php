<?php
namespace Fab\Media\FileUpload;

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
 * A interface for optimizing a file upload.
 */
interface ImageOptimizerInterface {

	/**
	 * Optimize the given uploaded image
	 *
	 * @param \Fab\Media\FileUpload\UploadedFileInterface $uploadedFile
	 * @return \Fab\Media\FileUpload\UploadedFileInterface
	 */
	public function optimize($uploadedFile);
}
