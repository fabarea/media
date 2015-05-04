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

use TYPO3\CMS\Vidi\Grid\GenericRendererComponent;

/**
 * Class for configuring a "Metadata" Grid Renderer in the Grid TCA.
 */
class MetadataRendererComponent extends GenericRendererComponent {

	/**
	 * Constructor for a "Metadata" Grid Renderer Component.
	 *
	 * @param array $configuration
	 */
	public function __construct($configuration = array()) {
		$className = 'Fab\Media\Grid\MetadataRenderer';
		parent::__construct($className, $configuration);
	}
}
