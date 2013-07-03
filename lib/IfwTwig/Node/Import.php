<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents an import node.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 */
class IfwTwig_Node_Import extends IfwTwig_Node
{
    public function __construct(IfwTwig_Node_Expression $expr, IfwTwig_Node_Expression $var, $lineno, $tag = null)
    {
        parent::__construct(array('expr' => $expr, 'var' => $var), array(), $lineno, $tag);
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
            ->write('')
            ->subcompile($this->getNode('var'))
            ->raw(' = ')
        ;

        if ($this->getNode('expr') instanceof IfwTwig_Node_Expression_Name && '_self' === $this->getNode('expr')->getAttribute('name')) {
            $compiler->raw("\$this");
        } else {
            $compiler
                ->raw('$this->env->loadTemplate(')
                ->subcompile($this->getNode('expr'))
                ->raw(")")
            ;
        }

        $compiler->raw(";\n");
    }
}
