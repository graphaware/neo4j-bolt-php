<?php

namespace GraphAware\Bolt\PackStream\Structure;

class TextElement extends AbstractElement
{
    /**
     * @var string
     */
    protected $text;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->text = (string) $string;
    }

    public function getLength()
    {
        return strlen($this->text);
    }

    public function getText()
    {
        return $this->text;
    }

    public function __toString()
    {
        return $this->text;
    }
}