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
use TYPO3\CMS\Core\Resource\File;

class TypeFacet implements FacetInterface
{
    protected string $name = 'type';

    protected string $label = 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:type';

    protected array $suggestions = [
        File::FILETYPE_TEXT => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:type_1',
        File::FILETYPE_IMAGE => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:type_2',
        File::FILETYPE_AUDIO => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:type_3',
        File::FILETYPE_VIDEO => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:type_4',
        File::FILETYPE_APPLICATION => 'LLL:EXT:media/Resources/Private/Language/locallang.xlf:type_5',
    ];

    protected string $dataType = '';

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
    public function setDataType($dataType): TypeFacet
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
        return new TypeFacet();
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

}
