<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class IfwTwig_Node_Expression_Test extends IfwTwig_Node_Expression_Call
{
    public function __construct(IfwTwig_NodeInterface $node, $name, IfwTwig_NodeInterface $arguments = null, $lineno)
    {
        parent::__construct(array('node' => $node, 'arguments' => $arguments), array('name' => $name), $lineno);
    }

    public function compile(IfwTwig_Compiler $compiler)
    {
        $name = $this->getAttribute('name');
        $test = $compiler->getEnvironment()->getTest($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'test');
        $this->setAttribute('thing', $test);
        if ($test instanceof IfwTwig_TestCallableInterface || $test instanceof IfwTwig_SimpleTest) {
            $this->setAttribute('callable', $test->getCallable());
        }

        $this->compileCallable($compiler);
    }
}
