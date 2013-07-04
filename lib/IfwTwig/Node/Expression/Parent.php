<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a parent node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class IfwTwig_Node_Expression_Parent extends IfwTwig_Node_Expression
{
    public function __construct($name, $lineno, $tag = null)
    {
        parent::__construct(array(), array('output' => false, 'name' => $name), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param IfwTwig_Compiler A IfwTwig_Compiler instance
     */
    public function compile(IfwTwig_Compiler $compiler)
    {
        if ($this->getAttribute('output')) {
            $compiler
                ->addDebugInfo($this)
                ->write("\$this->displayParentBlock(")
                ->string($this->getAttribute('name'))
                ->raw(", \$context, \$blocks);\n")
            ;
        } else {
            $compiler
                ->raw("\$this->renderParentBlock(")
                ->string($this->getAttribute('name'))
                ->raw(", \$context, \$blocks)")
            ;
        }
    }
}
