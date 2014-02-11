<?php
namespace TYPO3\CMS\Media\ViewHelpers\Widget;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * View helper which return a Carousel Gallery based on the markup of Twitter Bootstrap.
 * JQuery is assuming to be loaded.
 */
class CarouselViewHelper extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetViewHelper {

	/**
	 * @var \TYPO3\CMS\Media\ViewHelpers\Widget\Controller\CarouselController
	 * @inject
	 */
	protected $controller;

	/**
	 * Returns an carousel widget
	 *
	 * @param int $width max width of the image.
	 * @param int $height max height of the image.
	 * @param array|string $categories categories to be taken as match.
	 * @param int $interval interval value of time between the slides. "O" means no automatic sliding.
	 * @param bool $caption whether to display the title and description or not.
	 * @param string $sort the field name to sort out.
	 * @param string $order the direction to sort.
	 * @return string
	 */
	public function render($width = 600, $height = 600, $categories = array(), $interval = 0, $caption = TRUE, $sort = '', $order = 'ASC') {
		return $this->initiateSubRequest();
	}
}
