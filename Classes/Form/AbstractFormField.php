<?php
namespace Fab\Media\Form;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Media\Exception\InvalidStringException;
use Fab\Media\Utility\DomElement;

/**
 * A class to render a field.
 */
abstract class AbstractFormField implements FormFieldInterface
{

    /**
     * @var string
     */
    protected $template = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * Store what child element will contain this element.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Attributes in sense of DOM attribute, e.g. class, style, etc...
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @return string
     */
    public function render()
    {
        return $this->template;
    }

    /**
     * Render the label if possible. Otherwise return an empty string.
     *
     * @return string
     */
    public function renderLabel()
    {
        $result = '';
        if ($this->label) {
            $template = '<label class="control-label" for="%s">%s</label>';

            if (strpos($this->label, 'LLL:') === 0) {
                $this->label = LocalizationUtility::translate($this->label, '');
            }

            $result = sprintf($template,
                $this->getId(),
                $this->label
            );
        }
        return $result;
    }

    /**
     * Render additional attribute for this DOM element.
     *
     * @return string
     */
    public function renderAttributes()
    {
        $result = '';
        if (!empty($this->attributes)) {
            foreach ($this->attributes as $attribute => $value) {
                $result .= sprintf('%s="%s" ',
                    htmlspecialchars($attribute),
                    htmlspecialchars($value)
                );
            }
        }
        return $result;
    }

    /**
     * Add an additional (DOM) attribute to be added to this template.
     *
     * @throws InvalidStringException
     * @param array $attribute associative array that contains attribute => value
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function addAttribute(array $attribute)
    {
        if (!empty($attribute)) {
            reset($attribute);
            $key = key($attribute);
            if (!is_string($key)) {
                throw new InvalidStringException('Not an associative array. Is not a key: ' . $key, 1356478742);
            }

            $this->attributes[$key] = $attribute[$key];
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return htmlspecialchars($this->value);
    }

    /**
     * @param string $value
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        $result = $this->name;
        if ($this->getPrefix()) {
            $result = sprintf('%s[%s]', $this->getPrefix(), $this->name);
        }
        return $result;
    }

    /**
     * @param string $name
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        if ($this->id === '') {
            $this->id = DomElement::getInstance()->formatId($this->getName());
        }
        return $this->id;
    }

    /**
     * @param string $id
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return \Fab\Media\Form\AbstractFormField
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

}
