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
 * Interface implemented by compiler classes.
 *
 * @package    twig
 * @author     Fabien Potencier <fabien@symfony.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface IfwTwig_CompilerInterface
{
    /**
     * Compiles a node.
     *
     * @param IfwTwig_NodeInterface $node The node to compile
     *
     * @return IfwTwig_CompilerInterface The current compiler instance
     */
    public function compile(IfwTwig_NodeInterface $node);

    /**
     * Gets the current PHP code after compilation.
     *
     * @return string The PHP code
     */
    public function getSource();
}
