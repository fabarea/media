<?php
namespace Fab\Media\Facet;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\TypeConverter\ContentToFileConverter;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;
use Fab\Vidi\Facet\FacetInterface;
use Fab\Vidi\Module\ModuleLoader;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Signal\AfterFindContentObjectsSignalArguments;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class for configuring a custom Facet item.
 * Beware this is a resource consuming facet as we have to interrogate the file system for every file.
 */
class ActionPermissionFacet implements FacetInterface
{

    /**
     * @var string
     */
    protected $name = '__action_permission';

    /**
     * @var string
     */
    protected $label = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:permission';

    /**
     * @var array
     */
    protected $suggestions = array(
        'r' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:facet.read_only',
        'w' => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:facet.write',
    );

    /**
     * @var string
     */
    protected $fieldNameAndPath = '';

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var bool
     */
    protected $canModifyMatcher = false;

    /**
     * Constructor of a Generic Facet in Vidi.
     *
     * @param string $name
     * @param string $label
     * @param array $suggestions
     * @param string $fieldNameAndPath
     */
    public function __construct($name = '', $label = '', array $suggestions = [], $fieldNameAndPath = '')
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getLanguageService()->sL($this->label);
    }

    /**
     * @return array
     */
    public function getSuggestions()
    {
        $suggestions = [];
        foreach ($this->suggestions as $key => $label) {
            $suggestions[] = array($key => $this->getLanguageService()->sL($label));
        }

        return $suggestions;
    }

    /**
     * @return bool
     */
    public function hasSuggestions()
    {
        return true;
    }

    /**
     * @param string $dataType
     * @return $this
     */
    public function setDataType($dataType)
    {
        $this->dataType = $dataType;
        return $this;
    }

    /**
     * @return bool
     */
    public function canModifyMatcher()
    {
        return $this->canModifyMatcher;
    }

    /**
     * @param Matcher $matcher
     * @param $value
     * @return Matcher
     */
    public function modifyMatcher(Matcher $matcher, $value)
    {
        return $matcher;
    }

    /**
     * @param AfterFindContentObjectsSignalArguments $signalArguments
     * @return array
     */
    public function modifyResultSet(AfterFindContentObjectsSignalArguments $signalArguments)
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

    /**
     * @return array
     */
    protected function getQueryParts()
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
     *
     * @param array $queryParts
     * @return string
     */
    protected function getPermissionValue(array $queryParts)
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
    static public function __set_state($states)
    {
        return new ActionPermissionFacet($states['name'], $states['label'], $states['suggestions'], $states['fieldNameAndPath']);
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * @return ContentToFileConverter
     * @throws \InvalidArgumentException
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance(ContentToFileConverter::class);
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @return ModuleLoader
     * @throws \InvalidArgumentException
     */
    protected function getModuleLoader()
    {
        return GeneralUtility::makeInstance(ModuleLoader::class);
    }

}
