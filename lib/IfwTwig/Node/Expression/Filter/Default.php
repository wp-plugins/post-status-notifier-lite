<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Returns the value or the default value when it is undefined or empty.
 *
 * <pre>
 *  {{ var.foo|default('foo item on var is not defined') }}
 * </pre>
 *
 * @package twig
 * @author  Fabien Potencier <fabien@symfony.com>
 */
class IfwTwig_Node_Expression_Filter_Default extends IfwTwig_Node_Expression_Filter
{
    public function __construct(IfwTwig_NodeInterface $node, IfwTwig_Node_Expression_Constant $filterName, IfwTwig_NodeInterface $arguments, $lineno, $tag = null)
    {
        $default = new IfwTwig_Node_Expression_Filter($node, new IfwTwig_Node_Expression_Constant('default', $node->getLine()), $arguments, $node->getLine());

        if ('default' === $filterName->getAttribute('value') && ($node instanceof IfwTwig_Node_Expression_Name || $node instanceof IfwTwig_Node_Expression_GetAttr)) {
            $test = new IfwTwig_Node_Expression_Test_Defined(clone $node, 'defined', new IfwTwig_Node(), $node->getLine());
            $false = count($arguments) ? $arguments->getNode(0) : new IfwTwig_Node_Expression_Constant('', $node->getLine());

            $node = new IfwTwig_Node_Expression_Conditional($test, $default, $false, $node->getLine());
        } else {
            $node = $default;
        }

        parent::__construct($node, $filterName, $arguments, $lineno, $tag);
    }

    public function compile(IfwTwig_Compiler $compiler)
    {
        $compiler->subcompile($this->getNode('node'));
    }
}