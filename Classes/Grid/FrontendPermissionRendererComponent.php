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

use Fab\Vidi\Grid\GenericColumn;

/**
 * Class for configuring a "Create Relation" Grid Renderer in the Grid TCA.
 */
class FrontendPermissionRendererComponent extends GenericColumn {

	/**
	 * Constructor for a "Create Relation" Grid Renderer Component.
	 */
	public function __construct() {
		$className = 'Fab\Media\Grid\FrontendPermissionRenderer';
		parent::__construct($className, array());
	}
}
