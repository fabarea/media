<?php
namespace TYPO3\CMS\Media\ViewHelpers\Widget;

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
