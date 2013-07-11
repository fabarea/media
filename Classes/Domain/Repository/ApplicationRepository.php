<?php
namespace TYPO3\CMS\Media\Domain\Repository;

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
 * Repository for accessing applications
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class ApplicationRepository extends \TYPO3\CMS\Media\Domain\Repository\AssetRepository {

	/**
	 * @var string
	 */
	protected $objectType = 'TYPO3\CMS\Media\Domain\Model\Application';

	/**
	 * Returns all Application of this repository.
	 *
	 * @return \TYPO3\CMS\Media\Domain\Model\Application[]
	 */
	public function findAll() {
		$this->setObjectType($this->objectType);
		return $this->findByType(\TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION);
	}

	/**
	 * Finds all Applications given specified matches.
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Matcher $matcher
	 * @param \TYPO3\CMS\Media\QueryElement\Order $order The order
	 * @param int $limit
	 * @param int $offset
	 * @return \TYPO3\CMS\Media\Domain\Model\Application[]
	 */
	public function findBy(\TYPO3\CMS\Media\QueryElement\Matcher $matcher, \TYPO3\CMS\Media\QueryElement\Order $order = NULL, $limit = NULL, $offset = NULL) {
		$matcher->addMatch('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION);
		return parent::findBy($matcher, $order, $limit, $offset);
	}

	/**
	 * Count all Applications given specified matches.
	 *
	 * @param \TYPO3\CMS\Media\QueryElement\Matcher $matcher
	 * @return int
	 */
	public function countBy(\TYPO3\CMS\Media\QueryElement\Matcher $matcher) {
		$matcher->addMatch('type', \TYPO3\CMS\Core\Resource\File::FILETYPE_APPLICATION);
		return parent::countBy($matcher);
	}

	/**
	 * Dispatches magic methods (findBy[Property]())
	 *
	 * @param string $methodName The name of the magic method
	 * @param string $arguments The arguments of the magic method
	 * @throws \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException
	 * @return mixed
	 * @api
	 */
	public function __call($methodName, $arguments) {
		$this->setObjectType($this->objectType);
		return parent::__call($methodName, $arguments);
	}
}
?>