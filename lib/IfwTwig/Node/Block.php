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
 * Represents a block node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class IfwTwig_Node_Block extends IfwTwig_Node
{
    public function __construct($name, IfwTwig_NodeInterface $body, $lineno, $tag = null)
    {
        parent::__construct(array('body' => $body), array('name' => $name), $lineno, $tag);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param IfwTwig_Compiler A IfwTwig_Compiler instance
     */
    public function compile(IfwTwig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write(sprintf("public function block_%s(\$context, array \$blocks = array())\n", $this->getAttribute('name')), "{\n")
            ->indent()
        ;

        $compiler
            ->subcompile($this->getNode('body'))
            ->outdent()
            ->write("}\n\n")
        ;
    }
}