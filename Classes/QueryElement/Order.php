<?php
namespace TYPO3\CMS\Media\QueryElement;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * Order class for order that will apply to a query
 * @deprecated use TYPO3\CMS\Vidi\Persistence\Order instead
 */
class Order  {

	/**
	 * The orderings
	 *
	 * @var array
	 */
	protected $orderings = array();

	/**
	 * Constructs a new Order
	 *
	 * @para array $orders
	 */
	public function __construct($orders = array()) {
		foreach ($orders as $order => $direction) {
			$this->addOrdering($order, $direction);
		}
	}

	/**
	 * Add ordering
	 *
	 * @param string $order The order
	 * @param string $direction ASC / DESC
	 * @return void
	 */
	public function addOrdering($order, $direction) {
		$this->orderings[$order] = $direction;
	}

	/**
	 * Returns the order
	 *
	 * @return array The order
	 */
	public function getOrderings() {
		return $this->orderings;
	}
}
