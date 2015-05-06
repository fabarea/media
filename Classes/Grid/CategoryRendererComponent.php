<?php
namespace Fab\Media\Grid;
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

use Fab\Vidi\Grid\GenericRendererComponent;

/**
 * Class for configuring a "Category" Grid Renderer in the Grid TCA.
 */
class CategoryRendererComponent extends GenericRendererComponent {

	/**
	 * Constructor for a "Category" Grid Renderer Component.
	 */
	public function __construct() {
		$className = 'Fab\Media\Grid\CategoryRenderer';
		parent::__construct($className, array());
	}
}
