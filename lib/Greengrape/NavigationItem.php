<?php

namespace Greengrape;

class NavigationItem
{
    protected $_text = '';
    protected $_href = '';
    protected $_isActive = false;

    public function __construct($text, $href)
    {
        $this->setText($text);
        $this->setHref($href);
    }

    public function setText($text)
    {
        $this->_text = $text;
        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setHref($href)
    {
        $this->_href = $href;
        return $this;
    }

    public function getHref()
    {
        return $this->_href;
    }

    public function setIsActive($value)
    {
        $this->_isActive = (bool) $value;
        return $this;
    }

    public function getIsActive()
    {
        return $this->_isActive;
    }
}
