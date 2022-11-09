<?php

namespace Fab\Media\Facet;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Localization\LanguageService;
use Fab\Vidi\Facet\FacetInterface;
use Fab\Vidi\Persistence\Matcher;

class NumberOfReferencesFacet implements FacetInterface
{
    protected string $name = 'number_of_references';

    protected string $label = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:usage';

    protected array $suggestions = [
        '0', '1', '2', '3', 'etc...'
    ];
    protected string $dataType = 'sys_file';

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
        return $this->suggestions;
    }

    public function hasSuggestions(): bool
    {
        return true;
    }

    /**
     * @param string $dataType
     * @return $this
     */
    public function setDataType($dataType): NumberOfReferencesFacet
    {
        $this->dataType = $dataType;
        return $this;
    }

    public function canModifyMatcher(): bool
    {
        return false;
    }

    public function modifyMatcher(Matcher $matcher, $value): Matcher
    {
        return $matcher;
    }

    /**
     * Magic method implementation for retrieving state.
     *
     * @param array $states
     * @return $this
     */
    public static function __set_state($states)
    {
        return new NumberOfReferencesFacet();
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

}
