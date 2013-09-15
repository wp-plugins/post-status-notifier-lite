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

class IfwTwig_Node_Expression_AssignName extends IfwTwig_Node_Expression_Name
{
    /**
     * Compiles the node to PHP.
     *
     * @param IfwTwig_Compiler A IfwTwig_Compiler instance
     */
    public function compile(IfwTwig_Compiler $compiler)
    {
        $compiler
            ->raw('$context[')
            ->string($this->getAttribute('name'))
            ->raw(']')
        ;
    }
}
