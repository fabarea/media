<?php
namespace Fab\Media\View\Menu;

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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a dropdown menu for storage.
 */
class StorageMenu extends AbstractComponentView
{

    /**
     * @var \Fab\Vidi\Module\ModuleLoader
     * @inject
     */
    protected $moduleLoader;

    /**
     * Renders a dropdown menu for storage.
     *
     * @return string
     */
    public function render()
    {

        $output = '';
        if ($this->isDisplayed()) {
            $this->loadRequireJsCode();

            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addInlineLanguageLabelFile('EXT:media/Resources/Private/Language/locallang.xlf');

            $output = $this->renderStorageMenu();
        }

        return $output;
    }

    /**
     * @return string
     */
    protected function isDisplayed()
    {
        $isDisplayed = !$this->getMediaModule()->hasFolderTree() || $this->getMediaModule()->hasMediaFilePicker();
        if ($this->getModuleLoader()->hasPlugin()) {
            $isDisplayed = true;
        }
        return $isDisplayed;
    }

    /**
     * @return string
     */
    protected function renderStorageMenu()
    {

        $currentStorage = $this->getMediaModule()->getCurrentStorage();

        /** @var $storage \TYPO3\CMS\Core\Resource\ResourceStorage */
        $options = '';
        foreach ($this->getMediaModule()->getAllowedStorages() as $storage) {
            $selected = '';
            if ($currentStorage->getUid() == $storage->getUid()) {
                $selected = 'selected';
            }
            $options .= sprintf('<option value="%s" %s>%s %s</option>',
                $storage->getUid(),
                $selected,
                $storage->getName(),
                $storage->isOnline() ?
                    '' :
                    '(' . $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:offline') . ')'
            );
        }

        $parameters = GeneralUtility::_GET();
        $inputs = '';
        foreach ($parameters as $parameter => $value) {
            list($parameter, $value) = $this->computeParameterAndValue($parameter, $value);
            if ($parameter !== $this->moduleLoader->getParameterPrefix() . '[storage]') {
                $inputs .= sprintf('<input type="hidden" name="%s" value="%s" />', $parameter, $value);
            }
        }

        $template = '<form action="mod.php" id="form-menu-storage" method="get">
						%s
						<select name="%s[storage]" class="form-control" style="padding-right: 20px" id="menu-storage" onchange="$(\'#form-menu-storage\').submit()">%s</select>
					</form>';

        return sprintf($template,
            $inputs,
            $this->moduleLoader->getParameterPrefix(),
            $options
        );
    }

    /**
     * @return void
     */
    protected function loadRequireJsCode()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        $configuration['paths']['Fab/Media'] = '../typo3conf/ext/media/Resources/Public/JavaScript';
        $pageRenderer->addRequireJsConfiguration($configuration);
        $pageRenderer->loadRequireJsModule('Fab/Media/EditStorage');
    }

    /**
     * Compute parameter and value to be correctly encoded by the browser.
     *
     * @param string $parameter
     * @param mixed $value
     * @return array
     */
    protected function computeParameterAndValue($parameter, $value)
    {

        if (is_string($value)) {
            $result = array($parameter, $value);
        } else {
            $key = key($value);
            $value = current($value);
            $parameter = sprintf('%s[%s]', $parameter, $key);
            $result = $this->computeParameterAndValue($parameter, $value);
        }
        return $result;
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

}
