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
 * Represents a node that outputs an expression.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class IfwTwig_Node_Print extends IfwTwig_Node implements IfwTwig_NodeOutputInterface
{
    public function __construct(IfwTwig_Node_Expression $expr, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr), array(), $lineno, $tag);
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
            ->write('echo ')
            ->subcompile($this->getNode('expr'))
            ->raw(";\n")
        ;
    }
}