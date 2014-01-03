<?php
/**
 * Content partial class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

/**
 * Content
 *
 * This defines a content block in markdown
 * In the future we would have separate content classes for different formats, 
 * but right now we only support markdown
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ContentPartial extends Content
{
    /**
     * Template filename
     *
     * @var string
     */
    protected $_defaultTemplateFile = 'default.html';
}
