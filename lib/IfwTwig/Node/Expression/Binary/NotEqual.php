<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class IfwTwig_Node_Expression_Binary_NotEqual extends IfwTwig_Node_Expression_Binary
{
    public function operator(IfwTwig_Compiler $compiler)
    {
        return $compiler->raw('!=');
    }
}
