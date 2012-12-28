<?php
namespace TYPO3\CMS\Media\FileUpload;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * Handle file uploads via XMLHttpRequest.
 *
 * @see original implementation: https://github.com/valums/file-uploader/blob/master/server/php.php
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class StreamedFile implements \TYPO3\CMS\Media\FileUpload\UploadedFileInterface {

	/**
	 * @var string
	 */
	protected $inputName;

	/**
	 * @param string $inputName; defaults to the javascript default: 'qqfile'
	 * @return \TYPO3\CMS\Media\FileUpload\StreamedFile
	 */
	public function __construct($inputName = 'qqfile') {
		$this->inputName = $inputName;
	}

	/**
	 * Save the file to the specified path
	 *
	 * @param string $path
	 * @return boolean TRUE on success
	 */
	public function save($path) {
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		if ($realSize != $this->getSize()) {
			return false;
		}

		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		return true;
	}

	/**
	 * Get the original filename.
	 *
	 * @return string filename
	 */
	public function getName() {
		return $_GET[$this->inputName];
	}

	/**
	 * Get the file size
	 *
	 * @throws \Exception
	 * @return integer file-size in byte
	 */
	public function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])) {
			return (int) $_SERVER["CONTENT_LENGTH"];
		} else {
			throw new \Exception('Getting content length is not supported.');
		}
	}

	/**
	 * Get the file type.
	 *
	 * @return int
	 */
	public function getType() {
		// Not implemented.
	}}

?>