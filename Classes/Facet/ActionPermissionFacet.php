<?php

namespace Fab\Media\Facet;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Localization\LanguageService;
use Fab\Media\TypeConverter\ContentToFileConverter;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;
use Fab\Vidi\Facet\FacetInterface;
use Fab\Vidi\Module\ModuleLoader;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Signal\AfterFindContentObjectsSignalArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ActionPermissionFacet implements FacetInterface
{
    /**
     * @var string
     */
    protected string $name = '__action_permission';

    /**
     * @var string
     */
    protected string $label = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permission';

    /**
     * @var array
     */
    protected array $suggestions = array(
        'r' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:facet.read_only',
        'w' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:facet.write',
    );

    /**
     * @var string
     */
    protected string $dataType = 'sys_file';

    /**
     * @var bool
     */
    protected bool $canModifyMatcher = false;

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->getLanguageService()->sL($this->label);
    }

    public function getSuggestions(): array
    {
        $suggestions = [];
        foreach ($this->suggestions as $key => $label) {
            $suggestions[] = array($key => $this->getLanguageService()->sL($label));
        }

        return $suggestions;
    }

    public function hasSuggestions(): bool
    {
        return true;
    }

    /**
     * @param string $dataType
     * @return $this
     */
    public function setDataType($dataType): ActionPermissionFacet
    {
        $this->dataType = $dataType;
        return $this;
    }

    public function canModifyMatcher(): bool
    {
        return $this->canModifyMatcher;
    }

    public function modifyMatcher(Matcher $matcher, $value): Matcher
    {
        return $matcher;
    }

    public function modifyResultSet(AfterFindContentObjectsSignalArguments $signalArguments): array
    {
        if ($signalArguments->getDataType() === 'sys_file') {
            $queryParts = $this->getQueryParts();

            if (!empty($queryParts)) {
                $permission = $this->getPermissionValue($queryParts);

                if ($permission) {
                    // We are force to query the content repository again here without limit
                    $matcher = $signalArguments->getMatcher();
                    $order = $signalArguments->getOrder();
                    $objects = ContentRepositoryFactory::getInstance($this->dataType)->findBy($matcher, $order);

                    $filteredObjects = [];
                    foreach ($objects as $object) {
                        $file = $this->getFileConverter()->convert($object->getUid());
                        if ($permission === 'read' && !$file->checkActionPermission('write')) {
                            $filteredObjects[] = $object;
                        } elseif ($permission === 'write' && $file->checkActionPermission('write')) {
                            $filteredObjects[] = $object;
                        }
                    }

                    // Only take part of the array according to offset and limit.
                    $offset = $signalArguments->getOffset();
                    $limit = $signalArguments->getLimit();
                    $signalArguments->setContentObjects(array_slice($filteredObjects, $offset, $limit));

                    // Count number of records
                    $signalArguments->setNumberOfObjects(count($filteredObjects));
                    $signalArguments->setHasBeenProcessed(true);
                }
            }
        }

        return array($signalArguments);
    }

    protected function getQueryParts(): array
    {
        // Transmit recursive selection parameter.
        $parameterPrefix = $this->getModuleLoader()->getParameterPrefix();
        $parameters = GeneralUtility::_GP($parameterPrefix);

        $queryParts = [];
        if (!empty($parameters['searchTerm'])) {
            $query = rawurldecode($parameters['searchTerm']);
            $queryParts = json_decode($query, true);
        }

        return $queryParts;
    }

    /**
     * Retrieve the search permission value.
     */
    protected function getPermissionValue(array $queryParts): string
    {
        $permission = '';

        // Check also amongst labels.
        $labelReadOnly = $this->getLanguageService()->sL($this->suggestions['r']);
        $labelWrite = $this->getLanguageService()->sL($this->suggestions['w']);

        foreach ($queryParts as $queryPart) {
            $facetName = key($queryPart);
            $value = $queryPart[$facetName];
            if ($facetName === $this->name) {
                if ($value === 'r' || $value === $labelReadOnly) {
                    $permission = 'read';
                } elseif ($value === 'w' || $value === $labelWrite) {
                    $permission = 'write';
                }
            }
        }
        return $permission;
    }

    /**
     * Magic method implementation for retrieving state.
     *
     * @param array $states
     * @return $this
     */
    public static function __set_state($states)
    {
        return new ActionPermissionFacet();
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function getFileConverter(): ContentToFileConverter
    {
        return GeneralUtility::makeInstance(ContentToFileConverter::class);
    }

    protected function getModuleLoader(): ModuleLoader
    {
        return GeneralUtility::makeInstance(ModuleLoader::class);
    }
}
