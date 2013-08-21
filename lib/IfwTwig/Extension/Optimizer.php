<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class IfwTwig_Extension_Optimizer extends IfwTwig_Extension
{
    protected $optimizers;

    public function __construct($optimizers = -1)
    {
        $this->optimizers = $optimizers;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodeVisitors()
    {
        return array(new IfwTwig_NodeVisitor_Optimizer($this->optimizers));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'optimizer';
    }
}
