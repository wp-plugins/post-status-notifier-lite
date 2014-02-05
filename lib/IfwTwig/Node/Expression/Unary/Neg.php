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
class IfwTwig_Node_Expression_Unary_Neg extends IfwTwig_Node_Expression_Unary
{
    public function operator(IfwTwig_Compiler $compiler)
    {
        $compiler->raw('-');
    }
}
