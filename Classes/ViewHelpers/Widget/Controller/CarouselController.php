<?php
namespace TYPO3\CMS\Media\ViewHelpers\Widget\Controller;

/***************************************************************
*  Copyright notice
*
*  (c) 2012
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
 * Carousel Controller
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class CarouselController extends \TYPO3\CMS\Fluid\Core\Widget\AbstractWidgetController {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\ImageRepository
	 * @inject
	 */
	protected $imageRepository;

	/**
	 * @var array
	 */
	protected $categories = array();

	/**
	 * @var int
	 */
	protected $interval;

	/**
	 * @var int
	 */
	protected $height;

	/**
	 * @var int
	 */
	protected $width;

	/**
	 * @var bool
	 */
	protected $caption;

	/**
	 * @return void
	 */
	public function initializeAction() {
		$this->interval = (integer) $this->widgetConfiguration['interval'];
		$this->height = (integer) $this->widgetConfiguration['height'];
		$this->width = (integer) $this->widgetConfiguration['width'];
		$this->caption = strtolower($this->widgetConfiguration['caption'] == 'true') || $this->widgetConfiguration['caption'] == 1 ? TRUE : FALSE;

		$categories = $this->widgetConfiguration['categories'];
		if (is_string($categories) && strlen(trim($categories)) > 0) {
			$categories = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $categories);
		}
		if (!empty($categories)) {
			$this->categories = $categories;
		}
	}

	/**
	 * @return void
	 */
	public function indexAction() {

		/** @var $filter \TYPO3\CMS\Media\QueryElement\Filter */
		$filter = $this->objectManager->get('TYPO3\CMS\Media\QueryElement\Filter');

		foreach ($this->categories as $category) {
			$filter->addCategory($category);
		}
		$images = $this->imageRepository->findFiltered($filter);

		$this->view->assign('images', $images);
		$this->view->assign('interval', $this->interval);
		$this->view->assign('height', $this->height);
		$this->view->assign('width', $this->width);
		$this->view->assign('caption', $this->caption);
		$this->view->assign('viewId', uniqid());
	}
}

?>